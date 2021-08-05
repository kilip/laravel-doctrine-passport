<?php

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait HasUserTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\UserInterface")
     */
    protected ?User $user;

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}