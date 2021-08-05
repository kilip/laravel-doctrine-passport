<?php

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Passport\Contracts\Model\Client;

trait HasClientTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\ClientInterface"
     */
    protected Client $client;

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}