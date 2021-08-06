<?php

namespace Tests\LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\Client;

trait TestModelProperties
{
    protected object $model;

    /**
     * @param string $name
     * @param mixed  $expectedValue
     * @dataProvider getTestMutableProperties
     */
    final public function test_its_property_should_be_mutable(string $name, $expectedValue)
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

    public function getTestMutableProperties(): array
    {
        return [];
    }
}