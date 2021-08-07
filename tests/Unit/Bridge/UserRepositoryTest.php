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

use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Passport\Bridge\User as UserEntity;
use LaravelDoctrine\Passport\Bridge\UserRepository;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\Unit\UnitTestCase;

/**
 * @covers \LaravelDoctrine\Passport\Bridge\UserRepository
 */
class UserRepositoryTest extends UnitTestCase
{
    use RepositoryTestTrait;

    /**
     * @var Hasher|m\LegacyMockInterface|m\MockInterface
     */
    private $hasher;
    private UserRepository $repository;

    protected function setupRepository(): void
    {
        $this->hasher     = m::mock(Hasher::class);
        $this->repository = new UserRepository(
            $this->userManager,
            $this->hasher
        );
    }

    public function test_it_should_get_user_by_credentials()
    {
        $manager      = $this->userManager;
        $hasher       = $this->hasher;
        $repository   = $this->repository;
        $clientEntity = m::mock(ClientEntityInterface::class);
        $user         = $this->user;

        $manager->shouldReceive('findAndValidateForPassport')
            ->once()
            ->with('username', 'password')
            ->andReturn($user);
        $user->shouldReceive('getPassportUserId')
            ->once()->andReturn('user-id');
        $this->assertInstanceOf(
            UserEntity::class,
            $repository->getUserEntityByUserCredentials(
                'username',
                'password',
                'grant',
                $clientEntity
            )
        );
    }
}
