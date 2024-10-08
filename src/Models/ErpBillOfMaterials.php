<?php

namespace Faxt\Invenbin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;

/**
 * @OA\Schema(
 *     schema="BillOfMaterialsInput",
 *     title="ErpBom",
 *     description="Erp Bom model",
 *     @OA\Property(
 *         property="erp_product_id",
 *         type="integer",
 *         nullable=true,
 *         description="Foreign key ID of the associated ERP product",
 *         example=123
 *     ),
 *     @OA\Property(
 *         property="job_order_id",
 *         type="integer",
 *         nullable=true,
 *         description="Foreign key ID of a quotation details job order to create the associated ERP product from",
 *         example=456
 *     ),
 *     @OA\Property(
 *         property="bom_name",
 *         type="string",
 *         description="Name of the BOM",
 *         example="BOM-001"
 *     ),
 *     required={"bom_name"}
 * )
 *
 * @OA\Schema(
 *     schema="BillOfMaterialsOutput",
 *     title="ErpBom",
 *     description="Erp Bom model",
 *     @OA\Property(
 *         property="id",
 *         type="integer",
 *         description="ID of the BOM"
 *     ),
 *     @OA\Property(
 *         property="guid",
 *         type="string",
 *         description="GUID of the BOM"
 *     ),
 *     @OA\Property(
 *         property="erp_product_id",
 *         type="integer",
 *         description="Foreign key ID of the associated ERP product"
 *     ),
 *     @OA\Property(
 *         property="bom_name",
 *         type="string",
 *         description="Name of the BOM"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="integer",
 *         description="Foreign key ID of the user who last updated the BOM"
 *     ),
 *     @OA\Property(
 *         property="updated_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the BOM was last updated"
 *     ),
 *     @OA\Property(
 *         property="created_at",
 *         type="string",
 *         format="date-time",
 *         description="Date and time when the BOM was created"
 *     ),
 *     @OA\Property(
 *         property="product",
 *         type="object",
 *         ref="#/components/schemas/ProductShortOutput"
 *     ),
 *     @OA\Property(
 *         property="components",
 *         type="array",
 *         @OA\Items(ref="#/components/schemas/ProductShortOutput")
 *     )
 * )
 */


class ErpBillOfMaterials extends ErpBaseModel
{
    protected $table = 'erp_boms';


    protected $fillable = [
        'erp_product_id',
        'bom_name',
        'updated_by',
    ];


    public function product()
    {
        return $this->hasOne(ErpProduct::class,  'id', 'erp_product_id');
    }

    public function components()
    {
        return $this->hasMany(ErpComponent::class,'erp_bom_id');
    }

    /**
     * Convert the model instance to an array.
     *
     * @return array
     */
    public function toArray()
    {
        $data = parent::toArray();

        // Retrieve product data and add it to the array
        $productData = $this->product ? $this->product->toArray() : null;
        Arr::set($data, 'product', $productData);

        // Retrieve components data and add it to the array
        $componentsData = $this->components ? $this->components->toArray() : [];
        Arr::set($data, 'components', $componentsData);

        return $data;
    }
}

