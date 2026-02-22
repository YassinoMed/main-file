<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkflowExecution extends Model
{
    protected $fillable = [
        'workflow_id',
        'triggered_by',
        'model_id',
        'model_type',
        'execution_data',
        'status',
    ];

    protected $casts = [
        'execution_data' => 'array',
    ];

    public function workflow()
    {
        return $this->belongsTo(Workflow::class, 'workflow_id');
    }

    public function triggerer()
    {
        return $this->belongsTo(User::class, 'triggered_by');
    }

    public function model()
    {
        return $this->morphTo('model', 'model_type', 'model_id');
    }
}
