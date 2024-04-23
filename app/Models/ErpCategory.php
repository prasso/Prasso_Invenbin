<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="CategoryInputOutput",
 *     title="Category",
 *     description="Erp Category model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the category"
 *     ),
 *     @OA\Property(
 *         property="category_name",
 *         type="string",
 *         description="Name of the category"
 *     ),
 *     @OA\Property(
 *         property="image_file",
 *         type="string",
 *         description="Image file of the category"
 *     ),
 *     @OA\Property(
 *         property="parent_id",
 *         type="integer",
 *         description="ID of the parent category"
 *     ),
 *     @OA\Property(
 *         property="short_description",
 *         type="string",
 *         description="Short description of the category"
 *     ),
 *     @OA\Property(
 *         property="long_description",
 *         type="string",
 *         description="Long description of the category"
 *     ),
 *     @OA\Property(
 *         property="bom_id",
 *         type="integer",
 *         description="ID of the bill of material associated with the category"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="integer",
 *         description="ID of the user who last updated the category"
 *     ),
 * )
 */
class ErpCategory extends ErpBaseModel
{
    use HasFactory;

    protected $fillable = [
        'category_name',
        'image_file',
        'parent_id',
        'short_description',
        'long_description',
        'bom_id',
        'updated_by',
    ];

    public function parent(){
        return $this->belongsTo(ErpCategory::class, 'parent_id');
    }

    public function children(){
        return $this->hasMany(ErpCategory::class);
    }

    public function products()
    {
        return $this->belongsToMany(ErpProduct::class, 'erp_product_category_maps', 'erp_category_id', 'erp_product_id');
    }

    /**
     * Generate array for summary representation of the SalesInvoice model.
     * 
     * @return array
     */
    protected function summaryArray()
    {
        return [
            "id" => $this->id,
            "category_name" => $this->category_name,
            "parent" => $this->parent ? $this->parent->category_name : '' // Accessing the parent model directly
        ];
    }

    
}
