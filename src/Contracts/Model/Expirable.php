<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface Expirable
{
    public function getExpiresAt(): ?\DateTimeInterface;
}