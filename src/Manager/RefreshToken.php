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

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Passport\Events\RefreshTokenCreated;
use LaravelDoctrine\Passport\Contracts\Manager\RefreshToken as RefreshTokenManagerContract;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken as AccessTokenContract;
use LaravelDoctrine\Passport\Contracts\Model\RefreshToken as RefreshTokenContract;
use LaravelDoctrine\Passport\Exception\RuntimeException;

class RefreshToken implements RefreshTokenManagerContract
{
    use HasRepository;

    private Dispatcher $dispatcher;

    public function __construct(
        EntityManagerInterface $entityManager,
        Dispatcher $dispatcher,
        string $model
    ) {
        $this->em         = $entityManager;
        $this->class      = $model;
        $this->dispatcher = $dispatcher;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress InvalidStringClass
     */
    public function create(string $id, AccessTokenContract $accessToken, \DateTimeInterface $expiry, bool $revoked = false): object
    {
        $token = new $this->class($id, $accessToken, $expiry, $revoked);
        \assert($token instanceof RefreshTokenContract);
        $event = new RefreshTokenCreated((string) $token->getId(), (string) $accessToken->getId());

        $this->save($token);
        $this->dispatcher->dispatch($event);

        return $token;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function find($id): ?RefreshTokenContract
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function revokeRefreshToken($id): ?RefreshTokenContract
    {
        $token = $this->find($id);

        if (null !== $token) {
            $token->revoke();
            $this->save($token);
        }

        return $token;
    }

    /**
     * {@inheritDoc}
     */
    public function revokeRefreshTokensByAccessTokenId($tokenId)
    {
        /** @var RefreshTokenContract|null $refreshToken */
        $refreshToken = $this->getRepository()->findOneBy(['accessToken' => $tokenId]);

        if (null === $refreshToken) {
            throw RuntimeException::invalidRefreshTokenWithAccessTokenID($tokenId);
        }

        $refreshToken->revoke();
        $this->save($refreshToken);
    }

    /**
     * {@inheritDoc}
     */
    public function isRefreshTokenRevoked($id)
    {
        $token = $this->find($id);

        return null === $token ? true : $token->isRevoked();
    }
}
