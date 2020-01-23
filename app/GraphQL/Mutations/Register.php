<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use GraphQL\Type\Definition\ResolveInfo;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Joselfonseca\LighthouseGraphQLPassport\GraphQL\Mutations\BaseAuthResolver;
use Nuwave\Lighthouse\Support\Contracts\GraphQLContext;

class Register extends BaseAuthResolver
{
    /**
     * @param $rootValue
     * @param array                                                    $args
     * @param \Nuwave\Lighthouse\Support\Contracts\GraphQLContext      $context
     * @param \GraphQL\Type\Definition\ResolveInfo                     $resolveInfo
     *
     * @throws \Exception
     *
     * @return array
     */
    public function resolve($rootValue, array $args, GraphQLContext $context, ResolveInfo $resolveInfo)
    {
        $input = collect($args)->except('password_confirmation')->toArray();
        $input['password'] = Hash::make($input['password']);

        $user = User::create($input)->fresh();

        $user->assignRole('User');

        $credentials = $this->buildCredentials([
            'username' => $args[config('lighthouse-graphql-passport.username')],
            'password' => $args['password'],
        ]);

        $response = $this->makeRequest($credentials);
        $response['user'] = $user;

        event(new Registered($user));

        return $response;
    }
}
