<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface Revokable
{
    public function revoke(): void;

    public function isRevoked(): bool;
}