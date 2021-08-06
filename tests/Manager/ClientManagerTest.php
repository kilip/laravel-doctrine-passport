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

namespace Tests\LaravelDoctrine\Passport\Manager;

use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Events\ClientRemoved;
use LaravelDoctrine\Passport\Events\CreatePersonalAccessClient;
use LaravelDoctrine\Passport\Exception\RuntimeException;
use LaravelDoctrine\Passport\Manager;
use LaravelDoctrine\Passport\Manager\ClientManager;
use LaravelDoctrine\Passport\Model;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Manager\ClientManager
 * @covers \LaravelDoctrine\Passport\Exception\RuntimeException
 */
class ClientManagerTest extends TestCase
{
    use TestModelManager;

    /**
     * @var Manager\ClientManager
     */
    protected $manager;

    /**
     * @var ModelContracts\Client|m\LegacyMockInterface|m\MockInterface
     */
    private $client;

    /**
     * @var ModelContracts\User|m\LegacyMockInterface|m\MockInterface
     */
    private $user;

    public function configureManager(): void
    {
        $this->modelClass   = Model\AccessToken::class;
        $this->managerClass = Manager\ClientManager::class;
        $this->manager      = new $this->managerClass(
            $this->em,
            $this->dispatcher,
            'pac_id',
            'pac_secret',
            Model\AccessToken::class
        );
    }

    public function test_its_pac_id_and_secret_should_be_accessible()
    {
        $manager = $this->manager;

        $this->assertSame('pac_id', $manager->getPersonalAccessClientId());
        $this->assertSame('pac_secret', $manager->getPersonalAccessClientSecret());
    }

    protected function configureUserRepositoryMock()
    {
        $em       = $this->em;
        $userRepo = m::mock($userModel = ModelContracts\User::class);

        $em->shouldReceive('getRepository')
            ->with($userModel)->once()->andReturn($userRepo);
        $userRepo->shouldReceive('find')
            ->with('user-id')->andReturn($this->user);
    }

    protected function configureSaveMock(bool $useClientMock = true)
    {
        $em   = $this->em;
        $args = $useClientMock ? $this->client : m::type(ModelContracts\Client::class);
        $em->shouldReceive('persist')
            ->once()
            ->with($args);
        $em->shouldReceive('flush')
            ->once();
    }

    public function test_it_should_find_client_by_id()
    {
        $repository = $this->repository;
        $manager    = $this->manager;

        $repository->shouldReceive()
            ->find('id')
            ->once()->andReturns(null, $this->client);

        $this->assertNull($manager->find('id'));
    }

    public function test_it_should_find_active_client_by_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $client  = $this->client;

        $repo->shouldReceive()
            ->findOneBy([
                'id' => 'client-id',
                'revoked' => false,
            ])
            ->times(2)
            ->andReturns(null, $client);

        // client not exists
        $this->assertNull($manager->findActive('client-id'));

        // client exists
        $this->assertSame($client, $manager->findActive('client-id'));
    }

    public function test_it_should_find_instance_by_user_id_and_client_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $client  = $this->client;

        $repo->shouldReceive()
            ->findOneBy([
                'id' => 'client-id',
                'user' => 'user-id',
            ])->times(2)->andReturns(null, $client);

        $this->assertNull($manager->findForUser('client-id', 'user-id'));
        $this->assertSame(
            $client,
            $manager->findForUser('client-id', 'user-id')
        );
    }

    public function test_it_should_find_client_with_user_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $client  = $this->client;

        $repo->shouldReceive()
            ->findBy(
                ['user' => 'user-id'],
                ['name' => 'ASC']
            )->times(2)->andReturns([], [$client]);

        $this->assertSame([], (array) $manager->forUser('user-id')->toArray());
        $this->assertSame([$client], (array) $manager->forUser('user-id')->toArray());
    }

    public function test_it_should_find_active_client_by_user_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $client  = $this->client;

        $repo->shouldReceive()
            ->findBy(
                ['user' => 'user-id', 'revoked' => false],
                ['name' => 'ASC'],
            )->times(2)->andReturns([], [$client]);

        $this->assertSame([], $manager->activeForUser('user-id')->toArray());
        $this->assertSame([$client], $manager->activeForUser('user-id')->toArray());
    }

    public function test_it_should_get_the_personal_access_token_client_application()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $client  = $this->client;

        $repo->shouldReceive()
            ->find('pac_id')->once()
            ->andReturns($client);

        $repo->shouldReceive()
            ->findOneBy(
                [],
                ['id' => 'DESC']
            )->times(2)->andReturns($client, null);

        $this->assertSame($client, $manager->personalAccessClient());

        $manager = new ClientManager(
            $this->em,
            $this->dispatcher,
            null,
            'secret',
            $this->modelClass
        );
        $this->assertSame($client, $manager->personalAccessClient());

        $this->expectExceptionObject(RuntimeException::createPersonalAccessClientNotFound());

        $this->assertSame($client, $manager->personalAccessClient());
    }

    public function test_it_creates_new_client()
    {
        $manager = $this->manager;
        $user    = $this->user;
        $this->configureSaveMock(false);
        $this->configureUserRepositoryMock();

        $client = $manager->create(
            'user-id',
            'name',
            'redirect',
            'provider',
            true,
            true,
            false
        );

        $this->assertInstanceOf(Model\Client::class, $client);
        $this->assertSame($user, $client->getUser());
    }

    public function test_it_should_creates_new_personal_access_client()
    {
        $manager    = $this->manager;
        $dispatcher = $this->dispatcher;

        $this->configureSaveMock(false);
        $this->configureUserRepositoryMock();

        $dispatcher->shouldReceive('dispatch')
            ->with(m::type(CreatePersonalAccessClient::class));

        $client = $manager->createPersonalAccessClient('user-id', 'name', 'redirect');
        $this->assertInstanceOf(ModelContracts\Client::class, $client);
    }

    public function test_it_stores_a_new_password_grant_client()
    {
        $manager = $this->manager;

        $this->configureSaveMock(false);
        $this->configureUserRepositoryMock();

        $client = $manager->createPasswordGrantClient('user-id', 'name', 'redirect', 'provider');
        $this->assertIsObject($client);
        $this->assertSame($this->user, $client->getUser());
        $this->assertSame('name', $client->getName());
        $this->assertSame('redirect', $client->getRedirect());
        $this->assertSame('provider', $client->getProvider());
    }

    public function test_it_should_update_client()
    {
        $manager = $this->manager;
        $client  = $this->client;

        $this->configureSaveMock();

        $client->shouldReceive()->setName('name')->once();
        $client->shouldReceive()->setRedirect('redirect')->once();
        $manager->update($client, 'name', 'redirect');
    }

    public function test_it_should_regenerate_client_secret()
    {
        $manager = $this->manager;
        $client  = $this->client;

        $this->configureSaveMock();
        $client->shouldReceive()->setSecret(m::type('string'))->once();

        $manager->regenerateSecret($client);
    }

    public function test_it_should_determine_if_the_given_client_is_revoked()
    {
        $manager = $this->manager;
        $repo    = $this->repository;
        $client  = $this->client;

        $repo->shouldReceive('find')
            ->with('client-id')
            ->times(3)->andReturns(null, $client, $client);

        $client->shouldReceive('isRevoked')
            ->times(2)->andReturns(false, true);

        // true if client not found
        $this->assertTrue($manager->revoked('client-id'));

        // false if client exists and not revoked
        $this->assertFalse($manager->revoked('client-id'));

        // true if client exists and revoked
        $this->assertTrue($manager->revoked('client-id'));
    }

    public function test_it_delete_existing_client()
    {
        $manager    = $this->manager;
        $em         = $this->em;
        $client     = $this->client;
        $dispatcher = $this->dispatcher;

        $dispatcher->shouldReceive('dispatch')
            ->once()->with(m::type(ClientRemoved::class));
        $em->shouldReceive('remove')
            ->with($client)->once();
        $em->shouldReceive('flush')->once();

        $manager->delete($client);
    }
}
