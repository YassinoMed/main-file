<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'product_id',
        'production_bom_version_id',
        'warehouse_id',
        'work_center_id',
        'employee_id',
        'quantity_planned',
        'quantity_produced',
        'planned_start_date',
        'planned_end_date',
        'priority',
        'status',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'planned_start_date' => 'date',
        'planned_end_date' => 'date',
    ];

    public function product()
    {
        return $this->hasOne(ProductService::class, 'id', 'product_id');
    }

    public function bomVersion()
    {
        return $this->hasOne(ProductionBomVersion::class, 'id', 'production_bom_version_id');
    }

    public function warehouse()
    {
        return $this->hasOne(warehouse::class, 'id', 'warehouse_id');
    }

    public function workCenter()
    {
        return $this->hasOne(ProductionWorkCenter::class, 'id', 'work_center_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function operations()
    {
        return $this->hasMany(ProductionOrderOperation::class, 'production_order_id')->orderBy('sequence');
    }

    public function materials()
    {
        return $this->hasMany(ProductionMaterialMove::class, 'production_order_id');
    }

    public function qualityChecks()
    {
        return $this->hasMany(ProductionQualityCheck::class, 'production_order_id')->latest();
    }
}
