<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 * @OA\Schema(
 *     schema="ProductInputOutput",
 *     title="ErpProduct",
 *     description="Erp Product model",
 *     @OA\Property(
 *         property="sku",
 *         type="string",
 *         description="SKU of the product"
 *     ),
 *     @OA\Property(
 *         property="guid",
 *         type="string",
 *         description="GUID of the product"
 *     ),
 *     @OA\Property(
 *         property="product_name",
 *         type="string",
 *         description="Name of the product"
 *     ),
 *     @OA\Property(
 *         property="short_description",
 *         type="string",
 *         description="Short description of the product"
 *     ),
 *     @OA\Property(
 *         property="attribute_xml",
 *         type="string",
 *         description="XML attributes of the product"
 *     ),
 *     @OA\Property(
 *         property="stock_location",
 *         type="string",
 *         description="Location of the product in stock"
 *     ),
 *     @OA\Property(
 *         property="our_price",
 *         type="number",
 *         format="decimal",
 *         description="Price of the product set by us"
 *     ),
 *     @OA\Property(
 *         property="retail_price",
 *         type="number",
 *         format="decimal",
 *         description="Retail price of the product"
 *     ),
 *     @OA\Property(
 *         property="weight",
 *         type="number",
 *         format="decimal",
 *         description="Weight of the product"
 *     ),
 *     @OA\Property(
 *         property="currency_code",
 *         type="string",
 *         description="Currency code of the product price"
 *     ),
 *     @OA\Property(
 *         property="unit_of_measure_id",
 *         type="string",
 *         description="IDUnit of measure for the product"
 *     ),
 *     @OA\Property(
 *         property="admin_comments",
 *         type="string",
 *         description="Comments from the admin about the product"
 *     ),
 *     @OA\Property(
 *         property="length",
 *         type="number",
 *         format="decimal",
 *         description="Length of the product"
 *     ),
 *     @OA\Property(
 *         property="height",
 *         type="number",
 *         format="decimal",
 *         description="Height of the product"
 *     ),
 *     @OA\Property(
 *         property="width",
 *         type="number",
 *         format="decimal",
 *         description="Width of the product"
 *     ),
 *     @OA\Property(
 *         property="dimension_unit_id",
 *         type="string",
 *         description="ID Unit of measure for dimensions of the product"
 *     ),
 *     @OA\Property(
 *         property="list_order",
 *         type="integer",
 *         description="Order of the product in the list"
 *     ),
 *     @OA\Property(
 *         property="rating_sum",
 *         type="integer",
 *         description="Sum of ratings for the product"
 *     ),
 *     @OA\Property(
 *         property="total_rating_votes",
 *         type="integer",
 *         description="Total number of rating votes for the product"
 *     ),
 *     @OA\Property(
 *         property="default_image",
 *         type="string",
 *         description="URL or path to the default image of the product"
 *     ),
 *     @OA\Property(
 *         property="owned_by",
 *         type="integer",
 *         description="ID of the owner of the product"
 *     ),
 *     @OA\Property(
 *         property="inventory_count",
 *         type="integer",
 *         description="Inventory count of the product"
 *     ),
 *     @OA\Property(
 *         property="reorder_point",
 *         type="integer",
 *         description="Reorder point of the product"
 *     ),
 *     @OA\Property(
 *         property="product_status_id",
 *         type="integer",
 *         description="ID of the status of the product"
 *     ),
 *     @OA\Property(
 *         property="product_type_id",
 *         type="integer",
 *         description="ID of the type of the product"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="integer",
 *         description="User who last updated the product"
 *     )
 * )
 */

class ErpProduct extends ErpBaseModel
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'guid',
        'product_name',
        'short_description',
        'attribute_xml',
        'stock_location',
        'our_price',
        'retail_price',
        'weight',
        'currency_code',
        'unit_of_measure_id',
        'admin_comments',
        'length',
        'height',
        'width',
        'dimension_unit_id',
        'list_order',
        'rating_sum',
        'total_rating_votes',
        'default_image',
        'owned_by',
        'inventory_count',
        'reorder_point',
        'product_status_id',
        'product_type_id',
        'updated_by'
    ];

    public function status()
    {
        return $this->belongsTo(ErpProductStatus::class, 'product_status_id');
    }

    public function type()
    {
        return $this->belongsTo(ErpProductType::class, 'product_type_id');
    }

    public function images()
    {
        return $this->hasMany(ErpImage::class);
    }

    public function descriptors()
    {
        return $this->hasMany(ErpProductDescriptor::class);
    }

    public function categories()
    {
        return $this->belongsToMany(ErpCategory::class, 'erp_product_category_maps', 'erp_product_id', 'erp_category_id');
    }

    public function unitOfMeasure()
    {
        return $this->belongsTo(ErpUnitOfMeasure::class);
    }

    public function dimensionUnit()
    {
        return $this->belongsTo(ErpUnitOfMeasure::class);
    }

    public function usageLogs()
    {
        return $this->hasMany(ErpProductUsageLog::class);
    }

    /**
     * Generate array for summary representation of the model.
     * 
     * @return array
     */
    protected function summaryArray()
    {
       // return $this->getAttributes();
        if ($this->isSingleRecord) {
            // Return the complete model attributes
            return $this->getAttributes();
        } else {
           
        return [
            "id" => $this->id,
            "guid" => $this->guid,
            "product_name" => $this->product_name,
            "inventory_count" => $this->inventory_count
        ];
    }
    }
}
