<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpProductCategoryMap extends ErpBaseModel
{
    use HasFactory;   
    protected $table = 'erp_product_category_maps';



    protected $fillable = [
        'list_order',
        'is_featured',
        'updated_by',
    ];

    public function product()
    {
        return $this->belongsTo(ErpProduct::class, 'erp_product_id');
    }

    public function category()
    {
        return $this->belongsTo(ErpCategory::class, 'erp_category_id');
    }
}
