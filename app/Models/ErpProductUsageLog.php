<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ErpProductUsageLogInputOutput",
 *     title="ErpProductStatus",
 *     description="Erp Product Status model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         format="int64",
 *         description="The unique identifier for the product status",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="erp_product_id",
 *         type="integer",
 *         format="int64",
 *         description="The id of product used",
 *         example=1
 *     ),
 *     @OA\Property(
 *         property="adjustment",
 *         type="integer",
 *         description="amount inventory count of product was adjusted"
 *     ),
 *     @OA\Property(
 *         property="adjustment_type",
 *         type="string",
 *         description="the type of adjustment",
 *         example="Active"
 *     ),
 *     @OA\Property(
 *         property="reason",
 *         type="string",
 *         description="reason for the adjustment",
 *         example="Active"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="integer",
 *         description="ID of the user who entered this record"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the product status was created",
 *         readOnly=true
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="The date and time when the product status was last updated",
 *         readOnly=true
 *     )
 * )
 */
class ErpProductUsageLog extends ErpBaseModel
{
    use HasFactory;

    protected $table = 'erp_product_usage_log';

    protected $fillable = [
        'erp_product_id',
        'adjustment_type',
        'reason',
        'adjustment',
        'updated_by'
    ];

    public function product()
    {
        return $this->belongsTo(ErpProduct::class, 'erp_product_id');
    }
}
