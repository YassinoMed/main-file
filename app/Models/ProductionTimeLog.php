<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionTimeLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_operation_id',
        'employee_id',
        'work_center_id',
        'started_at',
        'ended_at',
        'minutes',
        'hourly_rate',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function operation()
    {
        return $this->hasOne(ProductionOrderOperation::class, 'id', 'production_order_operation_id');
    }

    public function employee()
    {
        return $this->hasOne(Employee::class, 'id', 'employee_id');
    }

    public function workCenter()
    {
        return $this->hasOne(ProductionWorkCenter::class, 'id', 'work_center_id');
    }
}
