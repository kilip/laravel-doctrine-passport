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

namespace Tests\LaravelDoctrine\Passport\Unit\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Illuminate\Contracts\Events\Dispatcher;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Contracts\Model\RefreshToken as RefreshTokenContract;
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

    /**
     * @var Dispatcher|m\LegacyMockInterface|m\MockInterface
     */
    protected $dispatcher;

    /**
     * @var ModelContracts\Client|m\LegacyMockInterface|m\MockInterface
     */
    private $client;

    /**
     * @var ModelContracts\User|m\LegacyMockInterface|m\MockInterface
     */
    private $user;

    /**
     * @var RefreshTokenContract|m\LegacyMockInterface|m\MockInterface
     */
    private $refreshToken;

    /**
     * @var ModelContracts\AccessToken|m\LegacyMockInterface|m\MockInterface
     */
    private $accessToken;

    /**
     * @var ModelContracts\AuthCode|m\LegacyMockInterface|m\MockInterface
     */
    private $authCode;

    public function setUp(): void
    {
        $this->em           = m::mock(EntityManagerInterface::class);
        $this->repository   = m::mock(EntityRepository::class);
        $this->dispatcher   = m::mock(Dispatcher::class);
        $this->client       = m::mock(ModelContracts\Client::class);
        $this->user         = m::mock(ModelContracts\User::class);
        $this->accessToken  = m::mock(ModelContracts\AccessToken::class);
        $this->refreshToken = m::mock(ModelContracts\RefreshToken::class);
        $this->authCode     = m::mock(ModelContracts\AuthCode::class);

        $this->configureManager();

        $this->em->shouldReceive()
            ->getRepository($this->modelClass)
            ->andReturns($this->repository);
    }

    abstract public function configureManager(): void;
}
