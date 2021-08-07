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

use Laravel\Passport\RefreshTokenRepository;
use LaravelDoctrine\Passport\Contracts\Model\RefreshToken as RefreshTokenContract;

/**
 * @see RefreshTokenRepository
 */
interface RefreshTokenManager extends CanSaveObject
{
    /**
     * Creates a new refresh token.
     *
     * @return object|RefreshTokenContract
     */
    public function create(
        string $id,
        \LaravelDoctrine\Passport\Contracts\Model\AccessToken $accessToken,
        \DateTimeInterface $expiry,
        bool $revoked = false
    ): object;

    /**
     * Gets a refresh token by the given ID.
     *
     * @param string $id
     *
     * @return RefreshTokenContract|null
     */
    public function find($id): ?RefreshTokenContract;

    /**
     * Revokes the refresh token.
     *
     * @param string $id
     *
     * @return RefreshTokenContract|null
     */
    public function revokeRefreshToken(string $id): ?RefreshTokenContract;

    /**
     * Revokes refresh tokens by access token id.
     *
     * @param string $tokenId
     *
     * @return void
     */
    public function revokeRefreshTokensByAccessTokenId($tokenId);

    /**
     * Checks if the refresh token has been revoked.
     *
     * @param string $id
     *
     * @return bool
     */
    public function isRefreshTokenRevoked($id);
}
