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
use LaravelDoctrine\Passport\Model\AuthCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\AuthCode
 * @covers \LaravelDoctrine\Passport\Model\Traits\ExpirableTrait
 * @covers \LaravelDoctrine\Passport\Model\Traits\HasClientTrait
 * @covers \LaravelDoctrine\Passport\Model\Traits\HasUserTrait
 * @covers \LaravelDoctrine\Passport\Model\Traits\ScopableTrait
 */
class AuthCodeTest extends TestCase
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

    /**
     * @var \DateTimeInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $expiresAt;

    protected function setUp(): void
    {
        $this->client    = $this->createMock(Client::class);
        $this->user      = $this->createMock(User::class);
        $this->expiresAt = new \DateTimeImmutable();

        $this->model = new AuthCode(
            'identifier',
            $this->client,
            $this->expiresAt,
            $this->user,
            ['scopes']
        );
    }

    public function getTestMutableProperties(): array
    {
        return [
            ['id', 'identifier'],
            ['client', Client::class],
            ['user', User::class],
            ['scopes', ['scopes']],
            ['isRevoked', false],
            ['expiresAt', \DateTimeInterface::class],
        ];
    }
}
