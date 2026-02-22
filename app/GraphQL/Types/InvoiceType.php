<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type as GraphQLType;

class InvoiceType extends GraphQLType
{
    protected $attributes = [
        'name' => 'Invoice',
        'description' => 'Invoice type',
        'model' => \App\Models\Invoice::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'invoice_id' => ['type' => Type::string()],
            'customer_id' => ['type' => Type::int()],
            'issue_date' => ['type' => Type::string()],
            'due_date' => ['type' => Type::string()],
            'status' => ['type' => Type::string()],
            'total' => ['type' => Type::float()],
        ];
    }
}
