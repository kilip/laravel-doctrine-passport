<?php

namespace LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientContract;

trait PersonalAccessClientTrait
{
    use IdentifiableTrait;
    use HasClientTrait;
    use Timestamps;

    public function __construct(
        ClientContract $client
    )
    {
        $this->client = $client;
    }
}