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

use LaravelDoctrine\Passport\Contracts\Model\AccessToken as AccessTokenContract;
use LaravelDoctrine\Passport\Contracts\Model\RefreshToken as RefreshTokenContract;
use LaravelDoctrine\Passport\Events\RefreshTokenCreated;
use LaravelDoctrine\Passport\Manager\RefreshToken as RefreshTokenManager;
use LaravelDoctrine\Passport\Model\RefreshToken as RefreshTokenModel;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Manager\RefreshToken
 */
class RefreshTokenTest extends TestCase
{
    use TestModelManager;

    private RefreshTokenManager $manager;

    public function configureManager(): void
    {
        $this->managerClass = RefreshTokenManager::class;
        $this->modelClass   = RefreshTokenModel::class;
        $this->refreshToken = m::mock(RefreshTokenContract::class);

        $this->manager = new RefreshTokenManager(
            $this->em,
            $this->dispatcher,
            $this->modelClass
        );

        $this->accessToken->shouldReceive()->getId()->andReturns('token-id');
    }

    protected function configureSaveMock(bool $useTokenMock = true)
    {
        $args = $useTokenMock ? $this->refreshToken : m::type(RefreshTokenContract::class);
        $em   = $this->em;
        $em->shouldReceive('persist')
            ->with($args)->once();
        $em->shouldReceive('flush')
            ->once();
    }

    protected function configureAccessTokenRepositoryMock()
    {
        $em       = $this->em;
        $repo     = m::mock($model = AccessTokenContract::class);

        $em->shouldReceive('getRepository')
            ->with($model)->once()->andReturn($repo);
        $repo->shouldReceive('find')
            ->with('token-id')->andReturn($this->accessToken);
    }

    public function test_it_should_create_new_refresh_token()
    {
        $manager     = $this->manager;
        $date        = new \DateTimeImmutable();
        $accessToken = $this->accessToken;
        $dispatcher  = $this->dispatcher;

        $this->configureSaveMock(false);

        $dispatcher->shouldReceive('dispatch')
            ->once()
            ->with(m::type(RefreshTokenCreated::class));

        $manager->create('refresh-id', $accessToken, $date);
    }

    public function test_it_should_find_refresh_token_by_id()
    {
        $manager = $this->manager;
        $repo    = $this->repository;
        $token   = $this->refreshToken;

        $repo->shouldReceive('find')
            ->with('id')
            ->times()->andReturns(null, $token);

        $this->assertNull($manager->find('id'));
        $this->assertSame($token, $manager->find('id'));
    }

    public function test_it_should_revokes_the_refresh_token()
    {
        $manager = $this->manager;
        $repo    = $this->repository;
        $token   = $this->refreshToken;

        $repo->shouldReceive('find')
            ->with('id')->once()->andReturn($token);
        $token->shouldReceive('revoke')
            ->once();

        $this->configureSaveMock();
        $manager->revokeRefreshToken('id');
    }

    public function test_it_revoke_refresh_token_by_access_token_id()
    {
        $manager      = $this->manager;
        $repo         = $this->repository;
        $refreshToken = $this->refreshToken;

        $repo->shouldReceive('findOneBy')
            ->with(['accessToken' => 'token-id'])
            ->once()->andReturn($refreshToken);

        $refreshToken->shouldReceive('revoke')
            ->once();

        $this->configureSaveMock();
        $manager->revokeRefreshTokensByAccessTokenId('token-id');
    }

    public function test_it_should_check_if_the_refresh_token_is_revoked()
    {
        $manager      = $this->manager;
        $repo         = $this->repository;
        $refreshToken = $this->refreshToken;

        $repo->shouldReceive('find')->with('id')
            ->andReturns(null, $refreshToken, $refreshToken);
        $refreshToken->shouldReceive('isRevoked')
            ->times(2)->andReturn(false, true);

        $this->assertTrue($manager->isRefreshTokenRevoked('id'));
        $this->assertFalse($manager->isRefreshTokenRevoked('id'));
        $this->assertTrue($manager->isRefreshTokenRevoked('id'));
    }
}
