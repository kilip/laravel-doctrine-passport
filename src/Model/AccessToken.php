<?php

namespace LaravelDoctrine\Passport\Model;


use LaravelDoctrine\Passport\Contracts\Model\AccessToken as AccessTokenContract;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AccessToken implements AccessTokenContract
{
    use AccessTokenTrait;

    public function __construct(
        string  $id,
        Client  $client,
        ?User   $user,
        ?string $name,
        ?array  $scopes,
        bool    $revoked = false
    )
    {
        $this->id = $id;
        $this->client = $client;
        $this->user = $user;
        $this->name = $name;
        $this->scopes = $scopes;
        $this->revoked = $revoked;
    }
}