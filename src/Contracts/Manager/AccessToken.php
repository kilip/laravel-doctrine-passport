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

namespace LaravelDoctrine\Passport\Contracts\Manager;

use Illuminate\Support\Collection;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;

/**
 * @see \Laravel\Passport\TokenRepository
 */
interface AccessToken
{
    /**
     * creates a new access token.
     *
     * @param string                   $id
     * @param ModelContracts\Client    $client
     * @param ModelContracts\User|null $user
     * @param string|null              $name
     * @param array|null               $scopes
     * @param bool                     $revoked
     *
     * @return ModelContracts\AccessToken
     */
    public function create(
        string $id,
        ModelContracts\Client $client,
        ?ModelContracts\User $user,
        ?string $name=null,
        ?array $scopes=null,
        bool $revoked = false): ModelContracts\AccessToken;

    /**
     * Get a token by the given ID.
     *
     * @param string $id
     *
     * @return ModelContracts\AccessToken|null
     */
    public function find(string $id): ?ModelContracts\AccessToken;

    /**
     * Get a token by the given user ID and token ID.
     *
     * @param string     $id
     * @param string|int $userId
     *
     * @return ModelContracts\AccessToken|null
     */
    public function findForUser(string $id, $userId): ?ModelContracts\AccessToken;

    /**
     * Get the token instances for the given user ID.
     *
     * @param string|int $userId
     *
     * @return Collection
     */
    public function forUser($userId): Collection;

    /**
     * Get a valid token instance for the given user and client.
     *
     * @param ModelContracts\User   $user
     * @param ModelContracts\Client $client
     *
     * @return ModelContracts\AccessToken|null
     */
    public function getValidToken(ModelContracts\User $user, ModelContracts\Client $client): ?ModelContracts\AccessToken;

    /**
     * Store the given token instance.
     *
     * @param ModelContracts\AccessToken $token
     * @param bool                       $andFlush
     *
     * @return void
     */
    public function save(ModelContracts\AccessToken $token, bool $andFlush = true);

    /**
     * Revoke an access token.
     *
     * @param string $id
     *
     * @return void
     */
    public function revokeAccessToken(string $id): void;

    /**
     * Check if the access token has been revoked.
     *
     * @param string $id
     *
     * @return bool
     */
    public function isAccessTokenRevoked(string $id): bool;

    /**
     * Find a valid token for the given user and client.
     *
     * @param ModelContracts\User   $user
     * @param ModelContracts\Client $client
     *
     * @return ModelContracts\AccessToken|null
     */
    public function findValidToken(ModelContracts\User $user, ModelContracts\Client $client): ?ModelContracts\AccessToken;
}
