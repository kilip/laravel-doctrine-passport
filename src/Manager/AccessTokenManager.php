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

namespace LaravelDoctrine\Passport\Manager;

use Carbon\Carbon;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\NonUniqueResultException;
use Illuminate\Support\Collection;
use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager as AccessTokenManagerContracts;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;

class AccessTokenManager implements AccessTokenManagerContracts
{
    use HasRepository;

    public function __construct(
        EntityManagerInterface $objectManager,
        string $accessTokenModelClass
    ) {
        $this->em    = $objectManager;
        $this->class = $accessTokenModelClass;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress MoreSpecificReturnType
     */
    public function create(string $id, ModelContracts\Client $client, ?ModelContracts\User $user, ?string $name = null, ?array $scopes = null, bool $revoked = false): ModelContracts\AccessToken
    {
        return new $this->class(
            $id,
            $client,
            $user,
            $name,
            $scopes,
            $revoked
        );
    }

    /**
     * {@inheritDoc}
     * @return ModelContracts\AccessToken|null
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function find(string $id): ?ModelContracts\AccessToken
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritDoc}
     * @param int|string $userId
     * @return ModelContracts\AccessToken|null
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function findForUser(string $id, $userId): ?ModelContracts\AccessToken
    {
        return $this->getRepository()->findOneBy(['id' => $id, 'user' => $userId]);
    }

    public function forUser($userId): Collection
    {
        $tokens = $this->getRepository()->findBy(['user' => $userId]);
        return new Collection($tokens);
    }

    /**
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function getValidToken(ModelContracts\User $user, ModelContracts\Client $client): ?ModelContracts\AccessToken
    {
        return $this->getRepository()->findOneBy([
            'user' => 'user-id',
            'client' => 'client-id',
        ]);
    }

    public function save(ModelContracts\AccessToken $token, bool $andFlush = true)
    {
        $om = $this->em;

        $om->persist($token);
        if ($andFlush) {
            $om->flush();
        }
    }

    public function revokeAccessToken(string $id): void
    {
        $token = $this->find($id);

        if (null !== $token) {
            $token->revoke();
            $this->save($token);
        }
    }

    public function isAccessTokenRevoked(string $id): bool
    {
        $token = $this->find($id);

        if (null !== $token) {
            return $token->isRevoked();
        }

        return true;
    }

    /**
     * @inheritDoc
     * @throws NonUniqueResultException
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function findValidToken(ModelContracts\User $user, ModelContracts\Client $client): ?ModelContracts\AccessToken
    {
        $qb = $this->getRepository()->createQueryBuilder('t');
        $qb->where('t.revoked = :revoked')
            ->andWhere('t.expires > :now')
            ->andWhere('t.user = :user')
            ->andWhere('t.client = :client')
            ->orderBy('t.expires|DESC')
            ->setParameter('revoked', false)
            ->setParameter('now', Carbon::now()->toDateTime())
            ->setParameter('user', $user->getId())
            ->setParameter('client', $client->getId())
        ;


        return $qb->getQuery()->getOneOrNullResult();
    }
}
