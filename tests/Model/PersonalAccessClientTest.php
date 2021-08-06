<?php

namespace Tests\LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Model\PersonalAccessClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\PersonalAccessClient
 * @covers \LaravelDoctrine\Passport\Model\PersonalAccessClientTrait
 */
class PersonalAccessClientTest extends TestCase
{
    private PersonalAccessClient $model;

    /**
     * @var Client|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->model = new PersonalAccessClient(
            $this->client
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
            ['client', Client::class],
        ];
    }
}
