<?php

namespace Tests\LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Model\AccessToken;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\AccessToken
 */
class AccessTokenTest extends TestCase
{
    private AccessToken $model;

    /**
     * @var Client|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    /**
     * @var User|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $user;

    public function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->user = $this->createMock(User::class);

        $this->model = new AccessToken(
            'identifier',
            $this->client,
            $this->user,
            'name',
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
            ['name', 'name'],
            ['scopes', ['scopes']],
            ['isRevoked', false]
        ];
    }

    public function test_it_revoke_access_token()
    {
        $this->model->revoke();
        $this->assertTrue($this->model->isRevoked());
    }
}
