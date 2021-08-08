<?php

/*
 * This file is part of the Laravel Doctrine Passport project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace LaravelDoctrine\Passport\Manager;

use Doctrine\Persistence\ObjectManager;
use LaravelDoctrine\Passport\Contracts\Manager\PersonalAccessClientManager as PersonalAccessClientManagerContract;
use LaravelDoctrine\Passport\Model\PersonalAccessClient;

/**
 * @psalm-suppress UnsafeInstantiation
 */
class PersonalAccessClientManager implements PersonalAccessClientManagerContract
{
    use HasRepository;

    /**
     * @param ObjectManager $om
     * @param string        $model
     * @psalm-param class-string<PersonalAccessClient> $model
     */
    public function __construct(
        ObjectManager $om,
        string $model
    ) {
        $this->om    = $om;
        $this->class = $model;
    }

    /**
     * {@inheritDoc}
     */
    public function create($client): void
    {
        /** @var class-string<PersonalAccessClient> $class */
        $class = $this->class;
        $pac   = new $class($client);
        $this->save($pac);
    }
}
