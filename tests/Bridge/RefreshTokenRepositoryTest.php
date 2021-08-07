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

namespace Tests\LaravelDoctrine\Passport\Bridge;

use Laravel\Passport\Bridge\AccessToken as AccessTokenEntity;
use Laravel\Passport\Events\RefreshTokenCreated;
use LaravelDoctrine\Passport\Bridge\RefreshTokenRepository;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Bridge\RefreshTokenRepository
 */
class RefreshTokenRepositoryTest extends TestCase
{
    use RepositoryTestTrait;

    private RefreshTokenRepository $repository;

    protected function setupRepository(): void
    {
        $this->repository = new RefreshTokenRepository(
            $this->refreshTokenManager,
            $this->tokenManager,
            $this->dispatcher
        );
    }

    public function test_it_should_create_new_refresh_token_entity()
    {
        $repository = $this->repository;

        $this->assertIsObject($repository->getNewRefreshToken());
    }

    public function test_it_persist_new_refresh_token()
    {
        $refreshManager = $this->refreshTokenManager;
        $tokenManager   = $this->tokenManager;
        $repository     = $this->repository;
        $dispatcher     = $this->dispatcher;
        $entity         = $repository->getNewRefreshToken();
        $expiry         = new \DateTimeImmutable();
        $tokenEntity    = m::mock(AccessTokenEntity::class);
        $token          = $this->token;

        $entity->setIdentifier('refresh-id');
        $entity->setExpiryDateTime($expiry);
        $entity->setAccessToken($tokenEntity);

        $tokenEntity->shouldReceive('getIdentifier')
            ->once()->andReturn('token-id');
        $tokenManager->shouldReceive('find')
            ->with('token-id')->andReturn($this->token);

        $refreshManager->shouldReceive('create')
            ->once()
            ->with(
                'refresh-id',
                $token,
                $expiry,
                false
            );

        $dispatcher->shouldReceive('dispatch')
            ->once()->with(m::type(RefreshTokenCreated::class));

        $repository->persistNewRefreshToken($entity);
    }

    public function test_it_should_revoke_refresh_token()
    {
        $this->refreshTokenManager->shouldReceive('revokeRefreshToken')
            ->with('id')
            ->once();
        $this->repository->revokeRefreshToken('id');
    }

    public function test_it_should_check_if_token_is_revoked()
    {
        $this->refreshTokenManager
            ->shouldReceive('isRefreshTokenRevoked')
            ->with('id')
            ->once();

        $this->repository->isRefreshTokenRevoked('id');
    }
}
