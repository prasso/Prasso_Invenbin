<?php

namespace Faxt\Invenbin\Services;

use App\Models\Site;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Prasso\Erp\Models\Activity;
use Prasso\Erp\Models\GoodsReceipt;
use Prasso\Erp\Models\InventoryTransaction;
use Prasso\Erp\Models\PurchaseOrder;
use Prasso\Erp\Models\SalesOrder;
use Prasso\Erp\Services\InventoryValuationService;

/**
 * Assembles the JSON payload for the `invenbin.dashboard` site page data
 * template (see docs/INVENBIN_DASHBOARD_PLAN.md). The shape of the returned
 * array must match `default_blank` on the `site_page_templates` row and the
 * keys consumed by the dashboard's inline script.
 */
class InvenbinDashboardService
{
    /**
     * Products at or below this fraction of their reorder point are "critical".
     */
    private const CRITICAL_STOCK_RATIO = 0.25;

    /**
     * Build the full dashboard payload for a site.
     */
    public function getDashboard(Site $site, ?User $user = null): array
    {
        return [
            'workspace_name' => $this->workspaceName($site),
            'user' => $this->userCard($site, $user),
            'date' => now()->format('l, F j, Y'),
            'greeting' => $this->greeting(),
            'first_name' => $user ? explode(' ', trim($user->name))[0] : '',
            'metrics' => $this->metrics($site),
            'attention_items' => $this->attentionItems($site),
            'activities' => $this->activities($site),
            'alerts' => $this->alerts($site),
        ];
    }

    public function getDashboardJson(Site $site, ?User $user = null): string
    {
        return json_encode($this->getDashboard($site, $user));
    }

    /**
     * Compact context for the AI copilot prompt — same data, trimmed.
     */
    public function getAssistantContext(Site $site): array
    {
        $data = $this->getDashboard($site);

        return [
            'workspace' => $data['workspace_name'],
            'metrics' => $data['metrics'],
            'attention_items' => array_map(fn ($i) => array_diff_key($i, ['id' => 1]), $data['attention_items']),
            'alerts' => $data['alerts'],
            'recent_activity' => $data['activities'],
        ];
    }

    private function workspaceName(Site $site): string
    {
        $team = $site->teamFromSite();

        return $team?->name ?: ($site->site_name ?? '');
    }

    private function userCard(Site $site, ?User $user): array
    {
        if (!$user) {
            return ['name' => '', 'initials' => '', 'role' => ''];
        }

        $initials = collect(explode(' ', trim($user->name)))
            ->filter()
            ->map(fn ($part) => strtoupper($part[0]))
            ->take(2)
            ->implode('');

        $role = 'Member';
        $team = $site->teamFromSite();
        if ($team) {
            $membership = DB::table('team_user')
                ->where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->first();
            if ($membership?->role) {
                $role = ucfirst($membership->role);
            } elseif ($team->user_id === $user->id || $user->isSuperAdmin()) {
                $role = 'Owner';
            }
        }

        return ['name' => $user->name, 'initials' => $initials, 'role' => $role];
    }

    private function greeting(): string
    {
        $hour = now()->hour;

        return match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };
    }

    /**
     * Site products keyed by their site_erp_products pivot id — the value the
     * erp_* transaction/item tables store in product_id. The pivot's own id
     * isn't selected by the belongsToMany relation, so map it from the table.
     */
    private function siteProductsByPivotId(Site $site)
    {
        $pivotMap = DB::table('site_erp_products')
            ->where('site_id', $site->id)
            ->pluck('erp_product_id', 'id'); // pivot_id => erp_product_id

        if ($pivotMap->isEmpty()) {
            return collect();
        }

        $products = \Faxt\Invenbin\Models\ErpProduct::whereIn('id', $pivotMap->values())
            ->get()
            ->keyBy('id');

        return $pivotMap->mapWithKeys(fn ($erpProductId, $pivotId) =>
            $products->has($erpProductId) ? [$pivotId => $products[$erpProductId]] : []
        );
    }

    /**
     * Net on-hand quantity per pivot product id from the inventory ledger.
     */
    private function ledgerQuantities(int $siteId)
    {
        return InventoryTransaction::forSite($siteId)
            ->selectRaw('product_id, SUM(CASE
                WHEN transaction_type IN ("purchase", "adjustment") THEN quantity
                WHEN transaction_type IN ("sale", "project_use") THEN -quantity
                ELSE 0 END) as net_qty')
            ->groupBy('product_id')
            ->pluck('net_qty', 'product_id');
    }

    /**
     * On-hand qty for a product: the ledger wins when the product has
     * transactions, otherwise the product's inventory_count column.
     */
    private function onHand($product, $ledgerQty): float
    {
        return $ledgerQty->has($product->pivot_id)
            ? (float) $ledgerQty[$product->pivot_id]
            : (float) ($product->inventory_count ?? 0);
    }

    /**
     * Unit cost for valuation: ledger average cost when present, else the
     * product's our_price.
     */
    private function unitCost($product, $ledgerValuation): float
    {
        $row = $ledgerValuation->get($product->pivot_id);
        if ($row && $row['net_quantity'] != 0) {
            return (float) $row['average_cost'];
        }

        return (float) ($product->our_price ?? 0);
    }

    private function metrics(Site $site): array
    {
        $products = $this->siteProductsByPivotId($site)
            ->map(function ($p, $pivotId) { $p->pivot_id = $pivotId; return $p; });
        $ledgerQty = $this->ledgerQuantities($site->id);
        $ledgerValuation = app(InventoryValuationService::class)
            ->getSiteValuation($site->id)['by_product'];
        $hasLedger = $ledgerQty->isNotEmpty();

        $itemsInStock = $products->sum(fn ($p) => $this->onHand($p, $ledgerQty));
        $inventoryValue = $products->sum(fn ($p) =>
            $this->onHand($p, $ledgerQty) * $this->unitCost($p, $ledgerValuation));

        // Month-over-month inventory value delta (ledger only).
        $valueDelta = null;
        if ($hasLedger) {
            $prior = app(InventoryValuationService::class)
                ->getSiteValuation($site->id, now()->subMonthNoOverflow()->endOfMonth());
            $priorValue = (float) ($prior['total_value'] ?? 0);
            if ($priorValue > 0) {
                $valueDelta = $this->formatPercent(($inventoryValue - $priorValue) / $priorValue * 100);
            }
        }

        // Units added/removed in the last 7 days.
        $weeklyDelta = (float) InventoryTransaction::forSite($site->id)
            ->where('transaction_date', '>=', now()->subDays(7))
            ->get()
            ->sum(fn ($t) => $t->net_quantity);

        $openPos = PurchaseOrder::forSite($site->id)->open();
        $openCount = (clone $openPos)->count();
        $dueSoon = (clone $openPos)
            ->where('expected_delivery_date', '<=', now()->addDays(7))
            ->count();

        [$rate, $rateDelta] = $this->fulfillmentRate($site->id);

        return [
            'inventory_value' => [
                'value' => $this->formatMoneyCompact($inventoryValue),
                'delta' => $valueDelta,
                'note' => 'vs. last month',
            ],
            'items_in_stock' => [
                'value' => number_format($itemsInStock),
                'delta' => $hasLedger ? $this->formatSignedNumber($weeklyDelta) : null,
                'note' => 'this week',
            ],
            'open_purchase_orders' => [
                'value' => number_format($openCount),
                'delta' => $dueSoon > 0 ? $dueSoon . ' due' : null,
                'delta_class' => 'warning',
                'note' => 'within 7 days',
            ],
            'fulfillment_rate' => [
                'value' => $rate !== null ? round($rate, 1) . '%' : '—',
                'delta' => $rateDelta,
                'note' => 'vs. last month',
            ],
        ];
    }

    /**
     * Shipped-on-time rate: sales orders whose actual_ship_date met
     * expected_ship_date, over all shipped/delivered orders. Returns
     * [current_rate_percent, mom_delta_string|null].
     */
    private function fulfillmentRate(int $siteId): array
    {
        $shipped = SalesOrder::forSite($siteId)
            ->whereIn('status', ['shipped', 'delivered'])
            ->whereNotNull('actual_ship_date')
            ->get(['expected_ship_date', 'actual_ship_date']);

        if ($shipped->isEmpty()) {
            return [null, null];
        }

        $rateFor = function ($orders) {
            if ($orders->isEmpty()) {
                return null;
            }
            $onTime = $orders->filter(fn ($o) =>
                $o->expected_ship_date && $o->actual_ship_date->lte($o->expected_ship_date)
            )->count();

            return $onTime / $orders->count() * 100;
        };

        $rate = $rateFor($shipped);

        $lastMonth = $shipped->filter(fn ($o) =>
            $o->actual_ship_date->isSameMonth(now()->subMonthNoOverflow())
        );
        $monthBefore = $shipped->filter(fn ($o) =>
            $o->actual_ship_date->isSameMonth(now()->subMonthsNoOverflow(2))
        );
        $lastRate = $rateFor($lastMonth);
        $priorRate = $rateFor($monthBefore);

        $delta = ($lastRate !== null && $priorRate !== null)
            ? $this->formatPercent($lastRate - $priorRate)
            : null;

        return [$rate, $delta];
    }

    /**
     * Stock rows for "Stock that needs attention": at/below reorder point
     * first (critical, then low), padded with the leanest healthy items so the
     * panel is never empty while products exist. Max 4 rows (mockup size).
     */
    private function attentionItems(Site $site): array
    {
        $products = $this->siteProductsByPivotId($site)
            ->map(function ($p, $pivotId) { $p->pivot_id = $pivotId; return $p; });
        if ($products->isEmpty()) {
            return [];
        }

        $ledgerQty = $this->ledgerQuantities($site->id);

        $rows = $products->map(function ($product) use ($ledgerQty) {
            $onHand = $this->onHand($product, $ledgerQty);
            $reorder = (float) ($product->reorder_point ?? 0);

            $pct = $reorder > 0 ? $onHand / $reorder * 100 : 100;
            $status = match (true) {
                $reorder > 0 && $pct <= self::CRITICAL_STOCK_RATIO * 100 => 'critical',
                $reorder > 0 && $onHand <= $reorder => 'low',
                default => 'healthy',
            };

            return [
                'id' => $product->id,
                'name' => $product->product_name,
                'sku' => $product->sku,
                'location' => $product->stock_location,
                'units' => (int) round($onHand),
                'pct' => (int) max(0, min(100, round($pct))),
                'status' => $status,
            ];
        });

        $order = ['critical' => 0, 'low' => 1, 'healthy' => 2];

        return $rows
            ->sortBy(fn ($r) => [$order[$r['status']], $r['pct']])
            ->take(4)
            ->values()
            ->all();
    }

    private function activities(Site $site): array
    {
        return Activity::forSite($site->id)
            ->orderBy('occurred_at', 'desc')
            ->limit(5)
            ->get()
            ->map(fn ($a) => [
                'icon' => $this->activityIcon($a),
                'title' => $this->activityTitle($a),
                'detail' => $this->activityDetail($a),
                'time' => $a->occurred_at ? $this->relativeTime($a->occurred_at) : '',
            ])
            ->all();
    }

    private function activityIcon(Activity $a): string
    {
        return match ($a->activity_type) {
            'purchase_order_approved', 'purchase_order_created', 'purchase_order' => 'PO',
            'shipment_received', 'goods_receipt', 'receipt' => 'IN',
            'cycle_count', 'count_completed' => 'CC',
            'sale', 'sales_order' => 'SO',
            default => strtoupper(substr(preg_replace('/[^a-z]/i', '', (string) $a->activity_type), 0, 2)) ?: 'A',
        };
    }

    private function activityTitle(Activity $a): string
    {
        $meta = $a->metadata ?? [];

        return $meta['title']
            ?? ucwords(str_replace('_', ' ', (string) $a->activity_type));
    }

    private function activityDetail(Activity $a): string
    {
        $meta = $a->metadata ?? [];

        return $meta['detail']
            ?? trim(($meta['reference'] ?? '') . ' ' . ($meta['party'] ?? ''))
            ?: ucfirst((string) $a->subject_type) . ($a->subject_id ? ' #' . $a->subject_id : '');
    }

    /**
     * Operational alerts: SKUs at/below reorder point, overdue open POs, and
     * the most recent posted goods receipt.
     */
    private function alerts(Site $site): array
    {
        $alerts = [];

        $needsReorder = collect($this->attentionItems($site))
            ->whereIn('status', ['critical', 'low'])
            ->count();
        if ($needsReorder > 0) {
            $alerts[] = [
                'dot' => 'amber',
                'title' => 'Reorder point reached',
                'detail' => $needsReorder . ' SKU' . ($needsReorder === 1 ? '' : 's') . ' need attention',
            ];
        }

        PurchaseOrder::forSite($site->id)
            ->open()
            ->where('expected_delivery_date', '<', now()->startOfDay())
            ->orderBy('expected_delivery_date')
            ->limit(3)
            ->get()
            ->each(function ($po) use (&$alerts) {
                $days = (int) $po->expected_delivery_date->diffInDays(now());
                $alerts[] = [
                    'dot' => 'red',
                    'title' => $po->po_number . ' is overdue',
                    'detail' => trim(($po->party?->name ?? 'Vendor') . ' · ' . $days . ' day' . ($days === 1 ? '' : 's')),
                ];
            });

        $receipt = GoodsReceipt::forSite($site->id)
            ->where('status', 'posted')
            ->orderBy('created_at', 'desc')
            ->first();
        if ($receipt) {
            $alerts[] = [
                'dot' => 'green',
                'title' => 'Receiving complete',
                'detail' => ($receipt->receipt_number ?? 'Receipt') . ' · '
                    . $receipt->items()->count() . ' line items',
            ];
        }

        return $alerts;
    }

    private function relativeTime(Carbon $at): string
    {
        $mins = (int) $at->diffInMinutes(now());
        if ($mins < 1) {
            return 'just now';
        }
        if ($mins < 60) {
            return $mins . ' min ago';
        }
        $hours = (int) $at->diffInHours(now());
        if ($hours < 24) {
            return $hours . ' hr ago';
        }

        return (int) $at->diffInDays(now()) . ' days ago';
    }

    private function formatMoneyCompact(float $value): string
    {
        return match (true) {
            abs($value) >= 1_000_000 => '$' . round($value / 1_000_000, 2) . 'M',
            abs($value) >= 1_000 => '$' . round($value / 1_000, 1) . 'K',
            default => '$' . number_format($value, 2),
        };
    }

    private function formatPercent(float $value): string
    {
        return ($value >= 0 ? '+' : '') . round($value, 1) . '%';
    }

    private function formatSignedNumber(float $value): string
    {
        return ($value >= 0 ? '+' : '') . number_format($value);
    }
}
