<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface Scopable
{
    public function getScopes(): ?array;
}