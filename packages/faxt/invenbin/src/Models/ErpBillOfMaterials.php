<?php

namespace Faxt\Invenbin\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
* @OA\Schema(
 *     schema="BillOfMaterialsInput",
 *     title="ErpBom",
 *     description="Erp Bom model",
 *     @OA\Property(
 *         property="erp_product_id",
 *         type="integer",
 *         description="Foreign key ID of the associated ERP product"
 *     ),
 *     @OA\Property(
 *         property="bom_name",
 *         type="string",
 *         description="Name of the BOM"
 *     )
 * )
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
 *     )
 * )
 */

class ErpBillOfMaterials extends ErpBaseModel
{
    use HasFactory;
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
}

