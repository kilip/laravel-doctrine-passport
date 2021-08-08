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

use Doctrine\Persistence\ObjectManager;
use Illuminate\Support\Collection;
use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager as AccessTokenManagerContracts;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;

class AccessTokenManager implements AccessTokenManagerContracts
{
    use HasRepository;

    public function __construct(
        ObjectManager $objectManager,
        string $model
    ) {
        $this->om    = $objectManager;
        $this->class = $model;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress MoreSpecificReturnType
     */
    public function create(string $id, ModelContracts\Client $client, ?ModelContracts\User $user, ?string $name = null, ?array $scopes = null, bool $revoked = false)
    {
        $token = new $this->class(
            $id,
            $client,
            $user,
            $name,
            $scopes,
            $revoked
        );
        $this->save($token);

        return $token;
    }

    /**
     * {@inheritDoc}
     *
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
     *
     * @param int|string $userId
     *
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
     * {@inheritDoc}
     */
    public function findValidTokenForUser($userId): Collection
    {
        $tokens = $this->getRepository()->findBy([
            'user' => $userId,
            'revoked' => false,
            'firstParty' => false,
        ]);

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
}
