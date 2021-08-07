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
use Laravel\Passport\Bridge\AccessToken as AccessTokenEntity;
use Laravel\Passport\Events\AccessTokenCreated;
use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager as AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\ClientManager as ClientManager;
use LaravelDoctrine\Passport\Contracts\Manager\UserManager as UserManager;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;

class AccessTokenRepository implements AccessTokenRepositoryInterface
{
    use ScopeConverter;

    protected AccessTokenManager $accessTokenManager;
    protected Dispatcher $events;
    protected ClientManager $clientManager;
    protected UserManager $userManager;

    public function __construct(
        AccessTokenManager $accessTokenManager,
        ClientManager $clientManager,
        UserManager $userManager,
        Dispatcher $dispatcher
    ) {
        $this->accessTokenManager = $accessTokenManager;
        $this->clientManager      = $clientManager;
        $this->userManager        = $userManager;
        $this->events             = $dispatcher;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress MixedArgument
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessTokenEntity($userIdentifier, $scopes, $clientEntity);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress MixedArgument
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $client = $this->clientManager->find($accessTokenEntity->getClient()->getIdentifier());
        $user   = $this->userManager->find($accessTokenEntity->getUserIdentifier());

        \assert(null !== $user);

        $token =  $this->accessTokenManager->create(
            $accessTokenEntity->getIdentifier(),
            $client,
            $user,
            null,
            $this->scopesToArray($accessTokenEntity->getScopes())
        );

        $event = new AccessTokenCreated($token->getId(), $user->getPassportUserId(), (string) $client->getId());
        $this->events->dispatch($event);
    }

    /**
     * {@inheritDoc}
     */
    public function revokeAccessToken($tokenId): void
    {
        $this->accessTokenManager->revokeAccessToken($tokenId);
    }

    /**
     * {@inheritDoc}
     */
    public function isAccessTokenRevoked($tokenId)
    {
        return $this->accessTokenManager->isAccessTokenRevoked($tokenId);
    }
}
