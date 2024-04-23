<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpProductDescriptor extends ErpBaseModel
{
    use HasFactory;
    protected $fillable = [
        'title',
        'descriptor',
        'is_bulleted_list',
        'list_order',
    ];

    public function product()
    {
        return $this->belongsTo(ErpProduct::class, 'erp_product_id');
    }
}
