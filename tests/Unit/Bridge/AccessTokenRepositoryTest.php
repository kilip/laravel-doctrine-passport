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

namespace Tests\LaravelDoctrine\Passport\Unit\Bridge;

use Laravel\Passport\Bridge\AccessToken as AccessTokenEntity;
use Laravel\Passport\Bridge\Client as ClientEntity;
use Laravel\Passport\Events\AccessTokenCreated;
use LaravelDoctrine\Passport\Bridge\AccessTokenRepository;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\Unit\UnitTestCase;

class AccessTokenRepositoryTest extends UnitTestCase
{
    use RepositoryTestTrait;

    protected AccessTokenRepository $repository;

    public function setupRepository(): void
    {
        $this->repository = new AccessTokenRepository(
            $this->tokenManager,
            $this->clientManager,
            $this->userManager,
            $this->dispatcher
        );
    }

    public function test_it_should_create_new_token()
    {
        $clientEntity = m::mock(ClientEntityInterface::class);
        $this->assertIsObject($this->repository->getNewToken($clientEntity, [$this->scope], 'user-id'));
    }

    public function test_it_should_creates_new_access_token()
    {
        $tokenManager = $this->tokenManager;

        $tokenManager->shouldReceive('create')
            ->with(
                'id',
                'user-id',
                'client-id',
                ['scopes'],
                m::type(\DateTimeInterface::class),
                false,
            );
    }

    public function test_it_should_persist_new_access_token()
    {
        $repo   = $this->repository;
        $client = m::mock(ClientModel::class);
        $user   = m::mock(UserModel::class);
        $entity = $this->buildAccessTokenEntity();
        $token  = m::mock(AccessToken::class);
        $scope  = $this->scope;
        $entity->setIdentifier('test');

        $token->shouldReceive('getId')
            ->once()->andReturn('token-id');
        $user->shouldReceive('getPassportUserId')->once()->andReturn('user-id');
        $client->shouldReceive('getId')->once()->andReturn('client-id');
        $this->clientManager->shouldReceive('find')
            ->with('client-id')
            ->once()->andReturn($client);
        $this->userManager->shouldReceive('find')
            ->with('user-id')
            ->once()->andReturn($user);
        $this->tokenManager->shouldReceive('create')
            ->withAnyArgs()->andReturn($token);

        $this->dispatcher->shouldReceive('dispatch')
            ->with(m::type(AccessTokenCreated::class));

        $repo->persistNewAccessToken($entity);
    }

    public function test_it_revoke_an_access_token()
    {
        $this->tokenManager->shouldReceive('revokeAccessToken')
            ->once()
            ->with('token-id');
        $this->repository->revokeAccessToken('token-id');
    }

    public function test_it_should_check_if_access_token_has_been_revoked()
    {
        $this->tokenManager->shouldReceive('isAccessTokenRevoked')
            ->once()->with('token-id')->andReturn(false);
        $this->assertFalse($this->repository->isAccessTokenRevoked('token-id'));
    }

    protected function buildAccessTokenEntity(): AccessTokenEntityInterface
    {
        $client = new ClientEntity('client-id', 'client', 'redirect');
        $scope  = $this->scope;

        $entity =  new AccessTokenEntity('user-id', [$scope], $client);
        $entity->setIdentifier('token-id');

        return $entity;
    }
}
