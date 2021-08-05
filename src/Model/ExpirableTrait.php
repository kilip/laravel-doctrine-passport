<?php

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;

trait ExpirableTrait
{
    /**
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected ?\DateTimeInterface $expiresAt = null;

    public function getExpiresAt(): ?\DateTimeInterface
    {
        return $this->expiresAt;
    }
}