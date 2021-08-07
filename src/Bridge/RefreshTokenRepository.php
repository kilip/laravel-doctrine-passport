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

namespace LaravelDoctrine\Passport\Bridge;

use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Passport\Bridge\RefreshToken as RefreshTokenEntity;
use Laravel\Passport\Events\RefreshTokenCreated;
use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager as AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\RefreshTokenManager as RefreshTokenManager;
use League\OAuth2\Server\Entities\RefreshTokenEntityInterface;
use League\OAuth2\Server\Repositories\RefreshTokenRepositoryInterface;

class RefreshTokenRepository implements RefreshTokenRepositoryInterface
{
    protected RefreshTokenManager $refreshTokenManager;
    protected AccessTokenManager $tokenManager;
    protected Dispatcher $dispatcher;

    /**
     * @param RefreshTokenManager $refreshTokenManager
     * @param Dispatcher          $dispatcher
     */
    public function __construct(
        RefreshTokenManager $refreshTokenManager,
        AccessTokenManager $tokenManager,
        Dispatcher $dispatcher
    ) {
        $this->refreshTokenManager = $refreshTokenManager;
        $this->tokenManager        = $tokenManager;
        $this->dispatcher          = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function getNewRefreshToken()
    {
        return new RefreshTokenEntity();
    }

    /**
     * {@inheritDoc}
     */
    public function persistNewRefreshToken(RefreshTokenEntityInterface $refreshTokenEntity): void
    {
        $token = $this->tokenManager->find($tokenId = $refreshTokenEntity->getAccessToken()->getIdentifier());

        \assert(null !== $token);

        $this->refreshTokenManager->create(
            $id = $refreshTokenEntity->getIdentifier(),
            $token,
            $refreshTokenEntity->getExpiryDateTime(),
            false
        );

        $event = new RefreshTokenCreated($id, $tokenId);
        $this->dispatcher->dispatch($event);
    }

    /**
     * {@inheritDoc}
     */
    public function revokeRefreshToken($tokenId): void
    {
        $this->refreshTokenManager->revokeRefreshToken($tokenId);
    }

    /**
     * {@inheritDoc}
     */
    public function isRefreshTokenRevoked($tokenId)
    {
        return $this->refreshTokenManager->isRefreshTokenRevoked($tokenId);
    }
}
