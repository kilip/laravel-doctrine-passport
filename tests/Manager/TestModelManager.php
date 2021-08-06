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

namespace Tests\LaravelDoctrine\Passport\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\Persistence\ObjectRepository;
use Mockery as m;

trait TestModelManager
{
    /**
     * @var EntityManagerInterface|m\LegacyMockInterface|m\MockInterface
     */
    protected $em;

    /**
     * @var EntityRepository|m\LegacyMockInterface|m\MockInterface
     */
    protected $repository;

    protected string $managerClass;

    protected string $modelClass;

    public function setUp(): void
    {
        $this->em         = m::mock(EntityManagerInterface::class);
        $this->repository = m::mock(EntityRepository::class);

        $this->configureManager();

        $this->em->shouldReceive()
            ->getRepository($this->modelClass)
            ->andReturns($this->repository);
    }

    abstract public function configureManager(): void;
}
