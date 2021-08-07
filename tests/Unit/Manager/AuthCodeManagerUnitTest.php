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

use LaravelDoctrine\Passport\Manager\AuthCodeManager;
use LaravelDoctrine\Passport\Model\AuthCode as AuthCodeModel;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\Unit\UnitTestCase;

/**
 * @covers \LaravelDoctrine\Passport\Manager\AuthCodeManager
 */
class AuthCodeManagerUnitTest extends UnitTestCase
{
    use TestModelManager;

    private AuthCodeManager $manager;

    public function configureManager(): void
    {
        $this->managerClass = AuthCodeManager::class;
        $this->modelClass   = AuthCodeModel::class;
        $this->manager      = new AuthCodeManager(
            $this->em,
            $this->modelClass
        );
    }

    protected function configureSaveMock(bool $useMockObject = true)
    {
        $this->em->shouldReceive('persist')
            ->once()
            ->with($useMockObject ? $this->authCode : m::type($this->modelClass));
        $this->em->shouldReceive('flush');
    }

    public function test_it_creates_new_auth_code()
    {
        $user    = $this->user;
        $client  = $this->client;
        $expiry  = new \DateTimeImmutable();
        $manager = $this->manager;

        $this->configureSaveMock(false);
        $manager->create('id', $user, $client, ['scope'], $expiry);
    }

    public function test_it_checks_if_auth_code_is_revoked()
    {
        $repository = $this->repository;
        $manager    = $this->manager;
        $authCode   = $this->authCode;

        $repository->shouldReceive('find')
            ->with('code-id')->times(3)
            ->andReturns(null, $authCode, $authCode);

        $authCode->shouldReceive('isRevoked')
            ->times(2)->andReturn(false, true);

        // true if code node exists
        $this->assertTrue($manager->isRevoked('code-id'));

        // false if code is not revoked
        $this->assertFalse($manager->isRevoked('code-id'));

        // true if code is revoked
        $this->assertTrue($manager->isRevoked('code-id'));
    }

    public function test_it_revoke_an_auth_code()
    {
        $code    = $this->authCode;
        $manager = $this->manager;
        $repo    = $this->repository;

        $repo->shouldReceive('find')
            ->once()->with('code-id')->andReturn($code);
        $code->shouldReceive('revoke')
            ->once();

        $this->configureSaveMock();

        $manager->revoke('code-id');
    }
}
