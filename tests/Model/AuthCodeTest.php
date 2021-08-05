<?php

namespace Tests\LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Model\AccessToken;
use LaravelDoctrine\Passport\Model\AuthCode;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\AuthCode
 * @covers \LaravelDoctrine\Passport\Model\ExpirableTrait
 * @covers \LaravelDoctrine\Passport\Model\HasClientTrait
 * @covers \LaravelDoctrine\Passport\Model\HasUserTrait
 * @covers \LaravelDoctrine\Passport\Model\ScopableTrait
 */
class AuthCodeTest extends TestCase
{
    /**
     * @var Client|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;
    /**
     * @var User|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $user;
    private AuthCode $model;

    /**
     * @var \DateTimeInterface|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $expiresAt;

    public function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->user = $this->createMock(User::class);
        $this->expiresAt = new \DateTimeImmutable();

        $this->model = new AuthCode(
            'identifier',
            $this->client,
            $this->expiresAt,
            $this->user,
            ['scopes']
        );
    }

    /**
     * @param string $name
     * @param mixed $expectedValue
     * @dataProvider getMutableProperties
     */
    public function testItsPropertyShouldBeMutable(string $name, $expectedValue)
    {
        $prefix = substr($name,0, 2) == 'is' ? '':'get';
        $result = call_user_func([$this->model, $prefix.$name]);
        if(!is_object($result)){
            $this->assertSame(
                $expectedValue,
                $result
            );
        }else{
            $this->assertInstanceOf(
                $expectedValue,
                $result
            );
        }
    }

    public function getMutableProperties(): array
    {
        return [
            ['id', 'identifier'],
            ['client', Client::class],
            ['user', User::class],
            ['scopes', ['scopes']],
            ['isRevoked', false],
            ['expiresAt', \DateTimeInterface::class]
        ];
    }
}
