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

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\QueryBuilder;
use Illuminate\Support\Collection;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Manager\AccessTokenManager;
use LaravelDoctrine\Passport\Model;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\TestCase;

class AccessTokenManagerTest extends TestCase
{
    use TestModelManager;

    private AccessTokenManager $manager;

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
        $this->modelClass = Model\AccessToken::class;
        $this->client     = m::mock(ModelContracts\Client::class);
        $this->user       = m::mock(ModelContracts\User::class);
        $this->manager    = new AccessTokenManager(
            $this->em,
            Model\AccessToken::class
        );
        $this->managerClass = AccessTokenManager::class;
    }

    public function test_it_should_create_new_token()
    {
        $token = $this->manager->create(
            'id',
            $this->client,
            $this->user,
            'name',
            ['scopes'],
            true
        );

        $this->assertInstanceOf(Model\AccessToken::class, $token);
    }

    public function test_it_should_find_token_by_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $token   = m::mock(ModelContracts\AccessToken::class);

        $repo->shouldReceive()->find('identifier')
            ->once()
            ->andReturns($token);

        $this->assertSame($token, $manager->find('identifier'));
    }

    public function test_it_should_find_token_with_token_id_and_user_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $token   = m::mock(ModelContracts\AccessToken::class);

        $repo->shouldReceive()->findOneBy([
            'id' => 'id',
            'user' => 'user',
        ])
            ->once()
            ->andReturns($token);

        $this->assertSame(
            $token,
            $manager->findForUser('id', 'user')
        );
    }

    public function test_it_should_find_tokens_by_user_id()
    {
        $repo    = $this->repository;
        $manager = $this->manager;
        $token   = m::mock(ModelContracts\User::class);
        $repo
            ->shouldReceive()
            ->findBy(['user' => 'user-id'])
            ->andReturns([$token]);

        $this->assertInstanceOf(
            Collection::class,
            $manager->forUser('user-id')
        );
    }

    public function test_it_should_get_valid_token_by_user_and_client_id()
    {
        $user    = m::mock(ModelContracts\User::class);
        $client  = m::mock(ModelContracts\Client::class);
        $token   = m::mock(ModelContracts\AccessToken::class);
        $repo    = $this->repository;
        $manager = $this->manager;

        $user
            ->shouldReceive()
            ->getId()
            ->andReturns('user-id');
        $client->shouldReceive()
            ->getId()
            ->andReturns('client-id');

        $repo
            ->shouldReceive()
            ->findOneBy(['user' => 'user-id', 'client' => 'client-id'])
            ->andReturns($token);

        $this->assertSame(
            $token,
            $manager->getValidToken($user, $client)
        );
    }

    public function test_it_should_revoke_access_token()
    {
        $repo    = $this->repository;
        $token   = m::mock(ModelContracts\AccessToken::class);
        $manager = $this->manager;
        $om      = $this->em;

        $om
            ->shouldReceive()
            ->persist($token)
            ->once();
        $om
            ->shouldReceive()
            ->flush()
            ->once();

        $repo
            ->shouldReceive()
            ->find('token-id')
            ->once()
            ->andReturns($token);
        $token
            ->shouldReceive()
            ->revoke()
            ->once();

        $manager->revokeAccessToken('token-id');
    }

    public function test_it_should_check_if_access_token_has_been_revoked()
    {
        $repo    = $this->repository;
        $token   = m::mock(ModelContracts\AccessToken::class);
        $manager = $this->manager;

        $repo
            ->shouldReceive()
            ->find('token-id')
            ->andReturn(null, $token);
        $token
            ->shouldReceive()
            ->isRevoked()
            ->once()
            ->andReturn(false);

        $this->assertTrue($manager->isAccessTokenRevoked('token-id'));
        $this->assertFalse($manager->isAccessTokenRevoked('token-id'));
    }

    public function test_it_should_store_token_instance()
    {
        $om      = $this->em;
        $manager = $this->manager;
        $token   = m::mock(ModelContracts\AccessToken::class);

        $om
            ->shouldReceive()
            ->persist($token)
            ->once();
        $om
            ->shouldReceive()
            ->flush()
            ->once();

        $manager->save($token);
    }

    public function test_it_should_find_valid_token_with_user_and_client_instance()
    {
        $repository       = $this->repository;
        $manager          = $this->manager;
        $userId           = 'user-id';
        $clientId         = 'client-id';
        $qb               = m::mock(QueryBuilder::class);
        $user             = m::mock(ModelContracts\User::class);
        $client           = m::mock(ModelContracts\Client::class);
        $token            = m::mock(ModelContracts\AccessToken::class);
        $query            = m::mock(AbstractQuery::class);

        $user->shouldReceive()->getId()->once()->andReturns($userId);
        $client->shouldReceive()->getId()->once()->andReturns($clientId);

        $repository->shouldReceive()
            ->createQueryBuilder('t')
            ->once()->andReturns($qb);

        $qb->shouldReceive()
            ->where('t.revoked = :revoked')
            ->once()->andReturn($qb);

        $qb->shouldReceive()->andWhere('t.expiresAt > :now')
            ->once()->andReturn($qb);

        $qb->shouldReceive()->andWhere('t.user = :user')
            ->once()->andReturn($qb);
        $qb->shouldReceive()->andWhere('t.client = :client')
            ->once()->andReturn($qb);

        $qb->shouldReceive()
            ->orderBy('t.expiresAt', 'DESC')
            ->once()->andReturn($qb);

        $qb->shouldReceive('setParameter')
            ->with('revoked', false)->andReturn($qb);
        $qb->shouldReceive('setParameter')
            ->with('now', m::type(\DateTimeInterface::class))
            ->andReturn($qb);
        $qb->shouldReceive()->setParameter('user', $userId)->andReturnSelf();
        $qb->shouldReceive()->setParameter('client', $clientId)->andReturnSelf();

        $qb->shouldReceive()
            ->getQuery()
            ->once()->andReturn($query);

        $query->shouldReceive()
            ->getOneOrNullResult()
            ->once()->andReturn($token);

        $manager->findValidToken($user, $client);
    }
}
