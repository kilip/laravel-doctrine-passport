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

namespace Tests\LaravelDoctrine\Passport\Functional\Manager;

use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\ORMTesting;
use Tests\LaravelDoctrine\Passport\TestCase;
use function app;

class AccessTokenManagerTest extends TestCase
{
    use ORMTesting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recreateDatabase();
    }

    public function test_it_should_handles_access_token()
    {
        $manager = app()->get(AccessTokenManager::class);

        $this->assertInstanceOf(AccessTokenManager::class, $manager);

        $user   = m::mock(ModelContracts\User::class);
        $client = m::mock(ModelContracts\Client::class);

        $user->shouldReceive()
            ->getId()->andReturns('user-id');
        $client->shouldReceive()
            ->getId()->andReturns('client-id');

        $this->assertNull($manager->findValidToken($user, $client));
    }
}
