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

namespace Tests\LaravelDoctrine\Passport\Unit\Model;

use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Model\AccessToken;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\AccessToken
 * @covers \LaravelDoctrine\Passport\Model\Traits\RevokableTrait
 * @covers \LaravelDoctrine\Passport\Model\Traits\IdentifiableTrait
 */
class AccessTokenTest extends TestCase
{
    use TestModelProperties;

    /**
     * @var Client|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var User|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $user;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->user   = $this->createMock(User::class);

        $this->model = new AccessToken(
            'identifier',
            $this->client,
            $this->user,
            'name',
            ['scopes']
        );
    }

    public function getTestMutableProperties(): array
    {
        return [
            ['id', 'identifier'],
            ['client', Client::class],
            ['user', User::class],
            ['name', 'name'],
            ['scopes', ['scopes']],
            ['isRevoked', false],
        ];
    }

    public function test_it_revoke_access_token()
    {
        $this->model->revoke();
        $this->assertTrue($this->model->isRevoked());
    }
}
