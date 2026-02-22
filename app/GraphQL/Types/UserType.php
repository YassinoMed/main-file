<?php

namespace App\GraphQL\Types;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Type;

class UserType extends Type
{
    protected $attributes = [
        'name' => 'User',
        'description' => 'A user type',
        'model' => \App\Models\User::class,
    ];

    public function fields(): array
    {
        return [
            'id' => ['type' => Type::int()],
            'name' => ['type' => Type::string()],
            'email' => ['type' => Type::string()],
            'type' => ['type' => Type::string()],
            'avatar' => ['type' => Type::string()],
            'created_at' => ['type' => Type::string()],
        ];
    }
}
