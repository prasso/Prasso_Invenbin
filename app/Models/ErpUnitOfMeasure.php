<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ErpUnitOfMeasure extends Model
{
    use HasFactory;
    protected $table = 'erp_unit_of_measure';

    protected $fillable = [
        'name',
        'symbol',
    ];

    
    public function products()
    {
        return $this->hasMany(ErpProduct::class);
    }
}
