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

namespace Tests\LaravelDoctrine\Passport\Unit\Bridge;

use LaravelDoctrine\Passport\Bridge\ClientRepository;
use PHPUnit\Framework\TestCase;

class ClientRepositoryTest extends TestCase
{
    use RepositoryTestTrait;

    private ClientRepository $repository;

    protected function setupRepository(): void
    {
        $this->repository = new ClientRepository(
            $this->clientManager,
            $this->dispatcher
        );
    }

    public function test_it_should_get_client_by_id()
    {
        $clientManager = $this->clientManager;
        $client        = $this->client;
        $repository    = $this->repository;

        $client->shouldReceive('getId')
            ->once()->andReturn('id');
        $client->shouldReceive('getName')
            ->once()->andReturn('name');
        $client->shouldReceive('getRedirect')
            ->once()->andReturn('redirect');
        $client->shouldReceive('confidential')
            ->once()->andReturn(true);
        $client->shouldReceive('getProvider')
            ->once()->andReturn('provider');

        $clientManager->shouldReceive('findActive')
            ->with('id')
            ->times(2)->andReturn(null, $client);

        // returns null if client not found
        $this->assertNull($repository->getClientEntity('id'));
        $this->assertIsObject($repository->getClientEntity('id'));
    }

    /**
     * @param bool   $expected
     * @param string $grantType
     * @param array  $clientAttributes
     * @dataProvider getTestValidateClient
     */
    public function test_it_should_validate_client(
        bool $expected,
        string $grantType = 'granted',
        array $clientAttributes = []
    ) {
        $clientManager = $this->clientManager;
        $repo          = $this->repository;
        $client        = $this->client;
        $clientId      = 'client-id';
        $clientSecret  = crypt('secret', 'salt');

        $clientAttributes = array_merge(
            [
                'firstParty' => false,
                'isPersonalAccessClient' => true,
                'confidential' => false,
                'isPasswordClient' => true,
                'getSecret' => $clientSecret,
            ],
            $clientAttributes
        );

        $clientManager->shouldReceive('findActive')
            ->with($clientId)
            ->andReturn($client);

        $client->shouldReceive('getGrantTypes')
            ->andReturn(['granted', 'authorization_code', 'personal_access', 'password', 'client_credentials']);

        foreach ($clientAttributes as $name => $value) {
            $client->shouldReceive($name)
                ->andReturn($value);
        }

        $method = $expected ? 'assertTrue' : 'assertFalse';
        \call_user_func(
            [$this, $method],
            $repo->validateClient($clientId, $clientSecret, $grantType)
        );
    }

    public function getTestValidateClient(): array
    {
        return [
            [true],
            [false, 'not-granted'],
            [true, 'authorization_code', ['firstParty' => false]],
            [true, 'personal_access', ['confidential' => true]],
            [true, 'client_credentials', ['confidential' => true]],
            [true, 'password'],
        ];
    }
}
