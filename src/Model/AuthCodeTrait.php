<?php

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait AuthCodeTrait
{
    use Timestamps;
    use RevokableTrait;
    use ExpirableTrait;
    use ScopableTrait;
    use HasUserTrait;
    use HasClientTrait;

    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id
     */
    protected string $id;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }
}