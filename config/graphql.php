<?php

return [
    'prefix' => 'graphql',
    'routes' => 'graphql',
    'route_name_prefix' => 'graphql.',
    'default_schema' => 'default',
    'schemas' => [
        'default' => [
            'query' => \App\GraphQL\Queries\QueryType::class,
            'mutation' => \App\GraphQL\Mutations\MutationType::class,
        ],
    ],
    'types' => [
        'User' => \App\GraphQL\Types\UserType::class,
        'Invoice' => \App\GraphQL\Types\InvoiceType::class,
        'Customer' => \App\GraphQL\Types\CustomerType::class,
        'Lead' => \App\GraphQL\Types\LeadType::class,
        'Employee' => \App\GraphQL\Types\EmployeeType::class,
        'Project' => \App\GraphQL\Types\ProjectType::class,
        'Task' => \App\GraphQL\Types\TaskType::class,
    ],
    'error_formatter' => [\Rebing\GraphQL\GraphQL::class, 'formatError'],
    'providers' => [
        \App\GraphQL\Providers\GraphQLServiceProvider::class,
    ],
];
