<?php

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;

trait RevokableTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $revoked = false;

    public function revoke(): void
    {
        $this->revoked = true;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }

}