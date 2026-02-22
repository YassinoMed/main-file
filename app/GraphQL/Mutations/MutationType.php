<?php

namespace App\GraphQL\Mutations;

use GraphQL\Type\Definition\Type;
use Rebing\GraphQL\Support\Mutation;

class MutationType extends Mutation
{
    protected $attributes = [
        'name' => 'mutation',
    ];

    public function type(): Type
    {
        return Type::boolean();
    }

    public function args(): array
    {
        return [];
    }

    public function resolve($root, $args)
    {
        return true;
    }
}
