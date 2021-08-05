<?php

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;

trait ScopableTrait
{
    /**
     * @ORM\Column(type="array", nullable=true)
     */
    protected ?array $scopes = null;

    /**
     * @return array|null
     */
    public function getScopes(): ?array
    {
        return $this->scopes;
    }
}