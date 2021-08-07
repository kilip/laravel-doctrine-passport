<?php

namespace LaravelDoctrine\Passport\Contracts\Manager;

use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;

interface PersonalAccessClient extends CanSaveObject
{
    /**
     * @param object|ClientModel $client
     */
    public function create($client): void;
}