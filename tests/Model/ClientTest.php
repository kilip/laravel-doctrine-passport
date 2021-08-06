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
 * @covers \LaravelDoctrine\Passport\Model\ClientTrait
 */
class ClientTest extends TestCase
{
    private Client $model;

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

    /**
     * @param string $name
     * @param mixed  $expectedValue
     * @dataProvider getMutableProperties
     */
    public function test_its_property_should_be_mutable(string $name, $expectedValue)
    {
        $prefix = 'is' == substr($name, 0, 2) ? '' : 'get';
        $result = \call_user_func([$this->model, $prefix.$name]);
        if ( ! \is_object($result)) {
            $this->assertSame(
                $expectedValue,
                $result
            );
        } else {
            $this->assertInstanceOf(
                $expectedValue,
                $result
            );
        }
    }

    public function getMutableProperties(): array
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
