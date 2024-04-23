<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ErpBomItem",
 *     title="ErpBomItem",
 *     description="Erp Bom Item model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the BOM item"
 *     ),
 *     @OA\Property(
 *         property="item_description",
 *         type="string",
 *         description="Description of the BOM item"
 *     ),
 *     @OA\Property(
 *         property="adjustment_units",
 *         type="decimal",
 *         nullable=true,
 *         description="Identifier of the BOM item"
 *     ),
 *     @OA\Property(
 *         property="erp_bom_id",
 *         type="integer",
 *         description="Foreign key ID of the associated BOM"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the BOM item was created"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the BOM item was last updated"
 *     )
 * )
 */

class ErpComponent extends ErpBaseModel
{
    use HasFactory;

    protected $fillable = [
        'item_description',
        'adjustment_units',
        'product_id',
        'erp_bom_id',
    ];
    
    public function product()
    {
        return $this->hasOne(ErpProduct::class);
    }

    public function bom()
    {
        return $this->belongsTo(ErpBillOfMaterials::class, 'erp_bom_id');
    }
}
