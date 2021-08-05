<?php

namespace LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\AuthCode as AuthCodeContracts;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;

/**
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AuthCode implements AuthCodeContracts
{
    use AuthCodeTrait;

    public function __construct(
        string $id,
        Client $client,
        \DateTimeInterface $expiresAt,
        ?User $user,
        ?array $scopes,
        bool $revoked = false
    )
    {

        $this->id = $id;
        $this->client = $client;
        $this->expiresAt = $expiresAt;
        $this->user = $user;
        $this->scopes = $scopes;
        $this->revoked = $revoked;
    }
}