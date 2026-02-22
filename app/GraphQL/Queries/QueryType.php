<?php

namespace App\GraphQL\Queries;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Facades\GraphQL;
use Rebing\GraphQL\Support\Query;

class QueryType extends Query
{
    protected $attributes = [
        'name' => 'query',
    ];

    public function type(): Type
    {
        return Type::listOf(GraphQL::type('User'));
    }

    public function args(): array
    {
        return [
            'id' => ['name' => 'id', 'type' => Type::int()],
            'email' => ['name' => 'email', 'type' => Type::string()],
        ];
    }

    public function resolve($root, $args)
    {
        $user = \Auth::user();
        if (! $user) {
            return null;
        }

        $creatorId = $user->creatorId();

        $query = \App\Models\User::where('created_by', $creatorId);

        if (isset($args['id'])) {
            return $query->where('id', $args['id'])->get();
        }

        if (isset($args['email'])) {
            return $query->where('email', $args['email'])->get();
        }

        return $query->get();
    }
}
