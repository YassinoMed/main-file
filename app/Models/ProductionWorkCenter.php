<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductionWorkCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'cost_per_hour',
        'created_by',
    ];

    public function productionOrders()
    {
        return $this->hasMany(ProductionOrder::class, 'work_center_id');
    }

    public function operations()
    {
        return $this->hasMany(ProductionOrderOperation::class, 'work_center_id');
    }

    public function timeLogs()
    {
        return $this->hasMany(ProductionTimeLog::class, 'work_center_id');
    }
}
