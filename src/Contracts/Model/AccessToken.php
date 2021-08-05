<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface AccessToken extends
    Revokable,
    Scopable,
    HasClient,
    HasUser
{
    public function revoke(): void;

    /**
     * @return string
     */
    public function getId(): string;
}