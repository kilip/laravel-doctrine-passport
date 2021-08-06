<?php

namespace LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\PersonalAccessClient as PersonalAccessClientContract;

class PersonalAccessClient implements PersonalAccessClientContract
{
    use PersonalAccessClientTrait;
}