<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface HasUser
{
    public function getUser(): ?User;
}