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

use Doctrine\ORM\EntityManagerInterface;
use LaravelDoctrine\Passport\Contracts\Manager\PersonalAccessClientManager as PersonalAccessClientManagerContract;

class PersonalAccessClientManager implements PersonalAccessClientManagerContract
{
    use HasRepository;

    /**
     * @param EntityManagerInterface $em
     * @param string                 $model
     */
    public function __construct(
        EntityManagerInterface $em,
        string $model
    ) {
        $this->em    = $em;
        $this->class = $model;
    }

    public function create($client): void
    {
        // TODO: Implement create() method.
    }
}
