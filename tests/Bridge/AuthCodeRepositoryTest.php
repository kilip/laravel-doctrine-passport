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

use Laravel\Passport\Bridge\Client as ClientEntity;
use LaravelDoctrine\Passport\Bridge\AuthCodeRepository;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\TestCase;

class AuthCodeRepositoryTest extends TestCase
{
    use RepositoryTestTrait;

    protected AuthCodeRepository $repository;

    public function setupRepository(): void
    {
        $this->repository = new AuthCodeRepository(
            $this->authCodeManager,
            $this->userManager,
            $this->clientManager,
            $this->dispatcher
        );
    }

    protected function buildAuthCodeEntity(): AuthCodeEntityInterface
    {
        $client = new ClientEntity('client-id', 'client', 'redirect');
        $scope  = $this->scope;

        $entity =  $this->repository->getNewAuthCode();
        $entity->setClient($client);
        $entity->setIdentifier('id');
        $entity->setUserIdentifier('user-id');
        $entity->addScope($scope);
        $entity->setExpiryDateTime(new \DateTimeImmutable());

        return $entity;
    }

    public function test_it_should_create_new_auth_code_entity()
    {
        $this->assertInstanceOf(
            AuthCodeEntityInterface::class,
            $this->repository->getNewAuthCode()
        );
    }

    public function test_it_should_persist_new_auth_code()
    {
        $authCode   = $this->authCode;
        $user       = $this->user;
        $client     = $this->client;
        $repository = $this->repository;
        $entity     = $this->buildAuthCodeEntity();

        $this->userManager->shouldReceive('find')
            ->with('user-id')->once()->andReturn($user);
        $this->clientManager->shouldReceive('find')
            ->with('client-id')->once()->andReturn($client);

        $this->authCodeManager->shouldReceive('create')
            ->once()
            ->with(
                $entity->getIdentifier(),
                $user,
                $client,
                m::type('array'),
                m::type(\DateTimeInterface::class)
            )->andReturn($authCode);

        $repository->persistNewAuthCode($entity);
    }

    public function test_it_revoke_an_auth_code()
    {
        $repository      = $this->repository;
        $authCodeManager = $this->authCodeManager;

        $authCodeManager->shouldReceive('revoke')
            ->once()
            ->with('auth-code-id');

        $repository->revokeAuthCode('auth-code-id');
    }

    public function test_if_the_auth_code_has_been_revoked()
    {
        $repository      = $this->repository;
        $authCodeManager = $this->authCodeManager;

        $authCodeManager->shouldReceive('isRevoked')
            ->once()->with('id')->andReturn(true);

        $this->assertTrue($repository->isAuthCodeRevoked('id'));
    }
}
