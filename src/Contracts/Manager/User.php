<?php

namespace LaravelDoctrine\Passport\Contracts\Manager;

use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;

interface User
{
    /**
     * @param null|int|string $userIdentifier
     * @return null|UserModel
     */
    public function find($userIdentifier): ?UserModel;
}