<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

use DateTime;

interface AccessToken
{
    public function revoke(): void;

    /**
     * @return string
     */
    public function getId(): string;

    /**
     * @return User|null
     */
    public function getUser(): ?User;

    /**
     * @return Client
     */
    public function getClient(): Client;

    /**
     * @return array|null
     */
    public function getScopes(): ?array;
}