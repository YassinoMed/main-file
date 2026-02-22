<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionOrderOperation extends Model
{
    use HasFactory;

    protected $fillable = [
        'production_order_id',
        'name',
        'sequence',
        'work_center_id',
        'planned_minutes',
        'actual_minutes',
        'status',
        'started_at',
        'finished_at',
        'created_by',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'finished_at' => 'datetime',
    ];

    public function productionOrder()
    {
        return $this->hasOne(ProductionOrder::class, 'id', 'production_order_id');
    }

    public function workCenter()
    {
        return $this->hasOne(ProductionWorkCenter::class, 'id', 'work_center_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(ProductionTimeLog::class, 'production_order_operation_id');
    }

    public function qualityChecks()
    {
        return $this->hasMany(ProductionQualityCheck::class, 'production_order_operation_id')->latest();
    }
}
