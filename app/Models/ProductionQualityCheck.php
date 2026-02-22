<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionQualityCheck extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'production_order_operation_id',
        'check_point',
        'result',
        'notes',
        'employee_id',
        'checked_at',
        'created_by',
    ];

    protected $casts = [
        'checked_at' => 'datetime',
    ];

    public function productionOrder()
    {
        return $this->hasOne(ProductionOrder::class, 'id', 'production_order_id');
    }

    public function operation()
    {
        return $this->hasOne(ProductionOrderOperation::class, 'id', 'production_order_operation_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }
}
