<?php

namespace LaravelDoctrine\Passport\Manager;

use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\Passport\Contracts\Manager\PersonalAccessClient as PersonalAccessClientManagerContract;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;

class PersonalAccessClientManager implements PersonalAccessClientManagerContract
{
    use HasRepository;

    /**
     * @param EntityManagerInterface $em
     * @param string $model
     */
    public function __construct(
        EntityManagerInterface $em,
        string $model
    )
    {
        $this->em = $em;
        $this->class = $model;
    }

    public function create($client): void
    {
        // TODO: Implement create() method.
    }
}