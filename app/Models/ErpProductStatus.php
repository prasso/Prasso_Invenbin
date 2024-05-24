<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="ProductStatusInput",
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
 *         property="status",
 *         type="string",
 *         description="The status of the product",
 *         example="Active"
 *     )
 * )
 * @OA\Schema(
 *     schema="ProductStatusOutput",
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
 *         property="status",
 *         type="string",
 *         description="The status of the product",
 *         example="Active"
 *     ),
 *     @OA\Property(
 *         property="updated_by",
 *         type="string",
 *         description="The user who last updated the product status",
 *         example="John Doe"
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

class ErpProductStatus extends ErpBaseModel
{
    use HasFactory;
    protected $fillable = [
        'status',
        'updated_by',
    ];

    public function products()
    {
        return $this->hasMany(ErpProduct::class,'product_status_id');
    }
}
