<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Workflow extends Model
{
    protected $fillable = [
        'created_by',
        'name',
        'description',
        'trigger_model',
        'trigger_conditions',
        'actions',
        'is_active',
    ];

    protected $casts = [
        'trigger_conditions' => 'array',
        'actions' => 'array',
        'is_active' => 'boolean',
    ];

    public function executions()
    {
        return $this->hasMany(WorkflowExecution::class, 'workflow_id');
    }

    public static function getAvailableTriggers()
    {
        return [
            \App\Models\Invoice::class => [
                'name' => 'Invoice',
                'description' => 'Invoice events',
            ],
            \App\Models\Project::class => [
                'name' => 'Project',
                'description' => 'Project events',
            ],
            \App\Models\Customer::class => [
                'name' => 'Customer',
                'description' => 'Customer events',
            ],
            \App\Models\Lead::class => [
                'name' => 'Lead',
                'description' => 'Lead events',
            ],
        ];
    }

    public static function getAvailableActions()
    {
        return [
            'email' => [
                'name' => 'Email',
                'description' => 'Send email',
            ],
            'notification' => [
                'name' => 'Notification',
                'description' => 'In-app notification',
            ],
            'task' => [
                'name' => 'Task',
                'description' => 'Create task',
            ],
            'update_field' => [
                'name' => 'Update Field',
                'description' => 'Update model field',
            ],
            'webhook' => [
                'name' => 'Webhook',
                'description' => 'Send webhook',
            ],
        ];
    }

    public function execute($model)
    {
        $execution = WorkflowExecution::create([
            'workflow_id' => $this->id,
            'triggered_by' => \Auth::id(),
            'model_id' => $model->id ?? null,
            'model_type' => get_class($model),
            'execution_data' => [
                'actions' => $this->actions,
            ],
            'status' => 'success',
        ]);

        return $execution;
    }
}
