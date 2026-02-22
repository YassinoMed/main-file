<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionMaterialMove extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'component_product_id',
        'warehouse_id',
        'required_qty',
        'reserved_qty',
        'consumed_qty',
        'reserved_at',
        'consumed_at',
        'created_by',
    ];

    protected $casts = [
        'reserved_at' => 'datetime',
        'consumed_at' => 'datetime',
    ];

    public function productionOrder()
    {
        return $this->hasOne(ProductionOrder::class, 'id', 'production_order_id');
    }

    public function component()
    {
        return $this->hasOne(ProductService::class, 'id', 'component_product_id');
    }

    public function warehouse()
    {
        return $this->hasOne(warehouse::class, 'id', 'warehouse_id');
    }
}
