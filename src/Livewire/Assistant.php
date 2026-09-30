<?php

namespace Faxt\Invenbin\Livewire;

use App\Models\Site;
use App\Models\SitePages;
use App\Services\BedrockAIService;
use Faxt\Invenbin\Services\InvenbinDashboardService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Livewire\Component;

/**
 * "Ask invenbin" operations copilot mounted inside the ERP dashboard site
 * page via loadLivewireComponent('invenbin.assistant', ...). Answers
 * questions grounded in the site's ERP dashboard data through Bedrock.
 */
class Assistant extends Component
{
    public $pageid;
    public $site;
    public $siteId;
    public $messages = [];
    public $userInput = '';
    public $isLoading = false;
    public $isOpen = false;
    public $authorized = false;

    protected $rules = [
        'userInput' => 'required|string|min:2|max:2000',
    ];

    public function mount($pageid = null)
    {
        $this->pageid = $pageid;
        $page = $pageid ? SitePages::find($pageid) : null;
        $this->site = $page ? Site::find($page->fk_site_id) : null;
        $this->siteId = $this->site?->id;
        $this->authorized = $this->userIsOnSiteTeam(Auth::user());
    }

    public function render()
    {
        return view('invenbin::livewire.assistant');
    }

    public function toggle()
    {
        $this->isOpen = !$this->isOpen;
    }

    public function askSuggestion($text)
    {
        $this->userInput = $text;
        $this->isOpen = true;
        $this->sendMessage();
    }

    public function sendMessage()
    {
        $user = Auth::user();

        if (!$user || !$this->site) {
            $this->addMessage('assistant', 'Site or user context not available.');
            return;
        }
        if (!$this->userIsOnSiteTeam($user)) {
            $this->addMessage('assistant', 'You do not have permission to use this feature.');
            return;
        }

        $this->validate();

        $this->addMessage('user', $this->userInput);
        $question = $this->userInput;
        $this->userInput = '';
        $this->isLoading = true;

        try {
            $context = app(InvenbinDashboardService::class)->getAssistantContext($this->site);
            $contextJson = json_encode($context, JSON_UNESCAPED_SLASHES);

            $history = collect($this->messages)
                ->slice(-8)
                ->map(fn ($m) => strtoupper($m['role']) . ': ' . $m['text'])
                ->implode("\n");

            $prompt = "You are the invenbin operations copilot embedded in the ERP dashboard for the workspace \"{$context['workspace']}\". "
                . "Answer questions about inventory, purchasing, orders, and operations using only the live data summary below. "
                . "Be concise and specific; cite numbers from the data. If the data doesn't cover the question, say so plainly.\n\n"
                . "FEATURES:\n"
                . "- Users can import existing data via CSV under ERP -> Import Data in the admin panel. Supported imports: "
                . "customers/vendors (parties), products with opening stock counts, chart of accounts with opening balances, "
                . "and sales/purchase order history. Column names are mapped automatically; a preview lets the user "
                . "review duplicates and fix row actions before anything is written.\n\n"
                . "LIVE DATA:\n{$contextJson}\n\n"
                . "CONVERSATION:\n{$history}\n\n"
                . "Reply with a short, plain-text answer.";

            $reply = app(BedrockAIService::class)->invokeModel([
                'prompt' => $prompt,
                'max_tokens' => 600,
            ]);

            $this->addMessage('assistant', trim((string) $reply) ?: 'No response.');
        } catch (\Exception $e) {
            Log::error('invenbin assistant error: ' . $e->getMessage());
            $this->addMessage('assistant', 'Sorry — something went wrong answering that. Try again in a moment.');
        }

        $this->isLoading = false;
    }

    private function addMessage(string $role, string $text): void
    {
        $this->messages[] = ['role' => $role, 'text' => $text];
    }

    private function userIsOnSiteTeam($user): bool
    {
        if (!$user || !$this->site) {
            return false;
        }
        if ($user->isSuperAdmin()) {
            return true;
        }

        foreach ($this->site->teams as $team) {
            if ($team->user_id === $user->id) {
                return true;
            }
            $isMember = DB::table('team_user')
                ->where('team_id', $team->id)
                ->where('user_id', $user->id)
                ->exists();
            if ($isMember) {
                return true;
            }
        }

        return false;
    }
}
