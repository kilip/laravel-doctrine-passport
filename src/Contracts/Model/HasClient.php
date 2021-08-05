<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface HasClient
{
    public function getClient(): Client;
}