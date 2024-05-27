<?php

namespace Faxt\Invenbin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ProductTypeInputOutput",
 *     title="ProductType",
 *     description="Erp ProductType model",
 *     @OA\Property(
 *         property="product_type",
 *         type="string",
 *         description="Name of the product type"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="integer",
 *         description="ID of the user who last updated the product type"
 *     ),
 * )
 */
class ErpProductType extends ErpBaseModel
{
    use HasFactory;

    protected $fillable = [
        'product_type',
        'updated_by',
    ];

    /**
     * Generate array for summary representation of the SalesInvoice model.
     *
     * @return array
     */
    protected function summaryArray()
    {
        return [
            "id" => $this->id,
            "product_type" => $this->product_type
        ];
    }

    public function parent()
    {
        return $this->belongsTo(ErpProductType::class, 'parent_id');
    }

    public function products()
    {
        return $this->hasMany(ErpProduct::class);
    }
}
