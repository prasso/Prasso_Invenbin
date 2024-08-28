<?php

namespace Faxt\Invenbin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Log;

/**
 *   @OA\Schema(
 *     schema="ProductOutput",
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
*      @OA\Property(
*          property="category",
*          type="array",
*          @OA\Items(
*               type="object",
*               @OA\Property(property="id", type="integer", example=62),
*               @OA\Property(property="category_name", type="string", example="Ink")
*             )
*         )
*     ),
*      @OA\Property(
*          property="vendor",
*          type="array",
*          @OA\Items(
*               type="object",
*               @OA\Property(property="id", type="integer", example=62),
*               @OA\Property(property="name", type="string", example="Acme Co")
*             )
*         )
*     ),
 *     @OA\Property(
 *         property="admin_comments",
 *         type="string",
 *         description="Comments/Notes about the product"
 *     ),
 *     @OA\Property(
 *         property="default_image",
 *         type="string",
 *         description="URL or path to the default image of the product"
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
 *     )
*      @OA\Property(
*          property="status",
*          type="array",
*          @OA\Items(
*               type="object",
*               @OA\Property(property="id", type="integer", example=62),
*               @OA\Property(property="status", type="string", example="Acme Co")
*             )
*         )
*     ),
*      @OA\Property(
*          property="type",
*          type="array",
*          @OA\Items(
*               type="object",
*               @OA\Property(property="id", type="integer", example=62),
*               @OA\Property(property="product_type", type="string", example="Acme Co")
*             )
*         )
*     )
 * )
 * 
 *  @OA\Schema(
 *     schema="ProductComponentInput",
 *     title="ErpProduct",
 *     description="Erp Product/Component model input creates a component if bom id included",
 *     @OA\Property(
 *         property="sku",
 *         type="string",
 *         description="SKU of the product"
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
 *         property="price",
 *         type="float",
 *         description="price of the product"
 *     ),
 *     @OA\Property(
 *         property="vendor_id",
 *         type="numeric",
 *         description="ID of vendor for the product ",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="erp_bom_id",
 *         type="integer",
 *         description="Bill Of Materials to associate product with (optional)"
 *     )    
 * )
 *
 * @OA\Schema(
 *     schema="ProductShortOutput",
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
 *         property="product_status",
 *         type="string",
 *         description="Status of the product"
 *     ),
 *     @OA\Property(
 *         property="product_type",
 *         type="string",
 *         description="Type of the product"
 *     ),
 *     @OA\Property(
 *         property="inventory_count",
 *         type="integer",
 *         description="Inventory count of the product"
 *     )
 * )
 *
 * @OA\Schema(
 *     schema="ProductInput",
 *     title="ErpProduct",
 *     description="Erp Product model",
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
 *         property="unit_of_measure_id",
 *         type="numeric",
 *         description="ID of Unit of measure for the product (1 is each)",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="category_id",
 *         type="numeric",
 *         description="ID of category for the product ",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="vendor_id",
 *         type="numeric",
 *         description="ID of vendor for the product ",
 *         example="1"
 *     ),
 *     @OA\Property(
 *         property="admin_comments",
 *         type="string",
 *         description="Comments/Notes about the product"
 *     )
 * )
 * 

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

    public function getIsComponentAttribute()
    {
        return $this->type->product_type === 'Component';
    }

    // Override the toArray method
    public function toArray()
    {
       if ($this->isSingleRecord){
        return $this->returnSingle();
       }

        // Call the parent's toArray method
        return parent::toArray();
    }

    private function returnSingle(){
        
        // Ensure relationships are loaded
        $this->loadmissing(['categories', 'vendor', 'status', 'type']);

        return [
            'id' => $this->id,
            'sku' => $this->sku,
            'guid' => $this->guid,
            'product_name' => $this->product_name,
            'short_description' => $this->short_description,
            'category' => $this->categories->map(function ($category) {
                return [
                    'id' => $category->id,
                    'category_name' => $category->category_name,
                ];
            })->toArray(),
            'admin_comments' => $this->admin_comments,
            'default_image' => $this->default_image,
            'inventory_count' => $this->inventory_count,
            'reorder_point' => $this->reorder_point,
            'status' => $this->status ? [
                'id' => $this->status->id,
                'status' => $this->status->status,
            ] : [],
            'type' => $this->type ? [
                'id' => $this->type->id,
                'product_type' => $this->type->product_type,
            ] : [],
            'unitOfMeasure' => $this->unitOfMeasure ? [
                'id' => $this->unitOfMeasure->id,
                'name' => $this->unitOfMeasure->name,
                'symbol' => $this->unitOfMeasure->symbol,
            ] : [], 
        ];
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
           return $this->returnSingle();
        } else {
            // Ensure relationships are loaded
            $this->loadmissing([ 'status', 'type']);

            return [
                "id" => $this->id,
                "guid" => $this->guid,
                "product_name" => $this->product_name,
                "sku" => $this->sku,
                "short_description" => $this->short_description,
                "product_status" => $this->status ? $this->status->status : null,
                "product_type" => $this->type ? $this->type->product_type : null, // Assuming the product_type field is available in the 'type' relationship
                "inventory_count" => $this->inventory_count,
                'unitOfMeasure' => $this->unitOfMeasure ? [
                    'id' => $this->unitOfMeasure->id,
                    'name' => $this->unitOfMeasure->name,
                    'symbol' => $this->unitOfMeasure->symbol,
                    ] : [], 
            ];
            

    }
    }
}
