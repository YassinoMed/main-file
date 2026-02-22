<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionBom extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'code',
        'name',
        'active_bom_version_id',
        'created_by',
    ];

    public function product()
    {
        return $this->hasOne(ProductService::class, 'id', 'product_id');
    }

    public function versions()
    {
        return $this->hasMany(ProductionBomVersion::class, 'production_bom_id');
    }

    public function activeVersion()
    {
        return $this->hasOne(ProductionBomVersion::class, 'id', 'active_bom_version_id');
    }
}
