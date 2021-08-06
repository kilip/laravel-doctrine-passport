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

use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Model\PersonalAccessClient;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\PersonalAccessClient
 * @covers \LaravelDoctrine\Passport\Model\Traits\PersonalAccessClientTrait
 */
class PersonalAccessClientTest extends TestCase
{
    use TestModelProperties;

    /**
     * @var Client|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $client;

    protected function setUp(): void
    {
        $this->client = $this->createMock(Client::class);
        $this->model  = new PersonalAccessClient(
            $this->client
        );
    }

    public function getTestMutableProperties(): array
    {
        return [
            ['client', Client::class],
        ];
    }
}
