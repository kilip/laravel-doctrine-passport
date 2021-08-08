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

namespace LaravelDoctrine\Passport\Bridge\ORM;

use Carbon\Carbon;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\NonUniqueResultException;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Contracts\TokenValidator;

class TokenManager implements TokenValidator
{
    private EntityManager $em;

    public function __construct(
        EntityManager $em
    ) {
        $this->em = $em;
    }

    /**
     * {@inheritDoc}
     *
     * @throws NonUniqueResultException
     */
    public function findValidToken(User $user, Client $client): ?AccessToken
    {
        $repo = $this->em->getRepository(AccessToken::class);
        $qb   = $repo->createQueryBuilder('t');

        $qb->where('t.revoked = :revoked')
            ->andWhere('t.expiresAt > :now')
            ->andWhere('t.user = :user')
            ->andWhere('t.client = :client')
            ->orderBy('t.expiresAt', 'DESC')
            ->setParameter('revoked', false)
            ->setParameter('now', Carbon::now()->toDateTime())
            ->setParameter('user', $user->getAuthIdentifier())
            ->setParameter('client', $client->getId());

        $ob = $qb->getQuery()->getOneOrNullResult();
        \assert($ob instanceof AccessToken);

        return $ob;
    }
}
