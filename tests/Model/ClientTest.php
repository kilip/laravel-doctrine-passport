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

namespace Tests\LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Model\Client;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\Client
 * @covers \LaravelDoctrine\Passport\Model\Traits\ClientTrait
 */
class ClientTest extends TestCase
{
    use TestModelProperties;

    /**
     * @var User|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $user;

    protected function setUp(): void
    {
        $this->user = $this->createMock(User::class);

        $this->model = new Client(
            $this->user,
            'name',
            'secret',
            'provider',
            'redirect',
            true,
            true,
            true
        );
    }

    public function getTestMutableProperties(): array
    {
        return [
            ['id', null],
            ['name', 'name'],
            ['secret', 'secret'],
            ['provider', 'provider'],
            ['redirect', 'redirect'],
            ['isPersonalAccessClient', true],
            ['isPasswordClient', true],
            ['user', User::class],
            ['isRevoked', true],
        ];
    }
}
