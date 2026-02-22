<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBomLine extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_bom_version_id',
        'component_product_id',
        'quantity',
        'scrap_percent',
        'created_by',
    ];

    public function bomVersion()
    {
        return $this->hasOne(ProductionBomVersion::class, 'id', 'production_bom_version_id');
    }

    public function component()
    {
        return $this->hasOne(ProductService::class, 'id', 'component_product_id');
    }
}
