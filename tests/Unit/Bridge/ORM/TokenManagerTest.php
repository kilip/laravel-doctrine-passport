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

namespace Tests\LaravelDoctrine\Passport\Unit\Bridge\ORM;

use Doctrine\ORM\AbstractQuery;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use LaravelDoctrine\Passport\Bridge\ORM\TokenManager;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\Unit\UnitTestCase;

/**
 * @covers \LaravelDoctrine\Passport\Bridge\ORM\TokenManager
 */
class TokenManagerTest extends UnitTestCase
{
    /**
     * @var EntityRepository|m\LegacyMockInterface|m\MockInterface
     */
    private $repository;
    /**
     * @var EntityManager|m\LegacyMockInterface|m\MockInterface
     */
    private $em;
    /**
     * @var QueryBuilder|m\LegacyMockInterface|m\MockInterface
     */
    private $qb;
    /**
     * @var AbstractQuery|m\LegacyMockInterface|m\MockInterface
     */
    private $query;
    private TokenManager $manager;

    protected function setUp(): void
    {
        $this->repository = m::mock(EntityRepository::class);
        $this->em         = m::mock(EntityManager::class);
        $this->qb         = m::mock(QueryBuilder::class);
        $this->query      = m::mock(AbstractQuery::class);

        $this->em->shouldReceive('getRepository')
            ->with(AccessToken::class)
            ->andReturns($this->repository);

        $this->manager = new TokenManager(
            $this->em
        );
    }

    /**
     * @var QueryBuilder|m\LegacyMockInterface|m\MockInterface
     */
    public function test_it_should_find_valid_token_with_user_and_client_instance()
    {
        $repository       = $this->repository;
        $manager          = $this->manager;
        $userId           = 'user-id';
        $clientId         = 'client-id';
        $user             = m::mock(User::class);
        $client           = m::mock(Client::class);
        $token            = m::mock(AccessToken::class);
        $qb               = $this->qb;
        $query            = $this->query;

        $user->shouldReceive('getAuthIdentifier')->once()->andReturns($userId);
        $client->shouldReceive('getId')->once()->andReturns($clientId);

        $repository->shouldReceive('createQueryBuilder')
            ->with('t')
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
