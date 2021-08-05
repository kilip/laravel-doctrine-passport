<?php

namespace LaravelDoctrine\Passport\Contracts\Model;

interface AuthCode extends
    HasUser,
    HasClient,
    Scopable,
    Revokable,
    Expirable
{

}