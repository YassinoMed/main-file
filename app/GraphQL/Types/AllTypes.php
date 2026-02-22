<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class CustomerType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Customer',
        'description' => 'Customer type',
        'model' => \App\Models\Customer::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'name' => ['type' => Type::string()],
            'email' => ['type' => Type::string()],
            'phone' => ['type' => Type::string()],
            'created_at' => ['type' => Type::string()],
        ];
    }
}

class LeadType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Lead',
        'description' => 'Lead type',
        'model' => \App\Models\Lead::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'name' => ['type' => Type::string()],
            'email' => ['type' => Type::string()],
            'phone' => ['type' => Type::string()],
            'status' => ['type' => Type::string()],
        ];
    }
}

class EmployeeType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Employee',
        'description' => 'Employee type',
        'model' => \App\Models\Employee::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'name' => ['type' => Type::string()],
            'email' => ['type' => Type::string()],
            'phone' => ['type' => Type::string()],
            'department' => ['type' => Type::string()],
        ];
    }
}

class ProjectType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Project',
        'description' => 'Project type',
        'model' => \App\Models\Project::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'name' => ['type' => Type::string()],
            'status' => ['type' => Type::string()],
            'start_date' => ['type' => Type::string()],
            'end_date' => ['type' => Type::string()],
        ];
    }
}

class TaskType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Task',
        'description' => 'Task type',
        'model' => \App\Models\ProjectTask::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'title' => ['type' => Type::string()],
            'status' => ['type' => Type::string()],
            'priority' => ['type' => Type::string()],
            'due_date' => ['type' => Type::string()],
        ];
    }
}
