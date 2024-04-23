<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpImage extends ErpBaseModel
{
    use HasFactory;

    protected $fillable = [
        'image_file',
        'list_order',
        'caption',
        'updated_by',
    ];

    public function product()
    {
        return $this->belongsTo(ErpProduct::class);
    }
}
