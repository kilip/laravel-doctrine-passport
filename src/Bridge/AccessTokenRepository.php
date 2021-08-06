<?php

namespace LaravelDoctrine\Passport\Bridge;

use Illuminate\Contracts\Events\Dispatcher;
use Laravel\Passport\Events\AccessTokenCreated;
use Laravel\Passport\TokenRepository;
use LaravelDoctrine\Passport\Contracts\Manager\AccessToken as AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\Client as ClientManager;
use LaravelDoctrine\Passport\Contracts\Manager\User as UserManager;
use LaravelDoctrine\Passport\Exception\RuntimeException;
use LaravelDoctrine\Passport\Model\Client;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use League\OAuth2\Server\Exception\UniqueTokenIdentifierConstraintViolationException;
use League\OAuth2\Server\Repositories\AccessTokenRepositoryInterface;
use Laravel\Passport\Bridge\AccessToken as AccessTokenEntity;


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
    )
    {
        $this->accessTokenManager = $accessTokenManager;
        $this->clientManager = $clientManager;
        $this->userManager = $userManager;
        $this->events = $dispatcher;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress MixedArgument
     */
    public function getNewToken(ClientEntityInterface $clientEntity, array $scopes, $userIdentifier = null)
    {
        return new AccessTokenEntity($userIdentifier, $scopes, $clientEntity);
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress MixedArgument
     */
    public function persistNewAccessToken(AccessTokenEntityInterface $accessTokenEntity): void
    {
        $client = $this->clientManager->find($accessTokenEntity->getClient()->getIdentifier());
        $user = $this->userManager->find($accessTokenEntity->getUserIdentifier());

        assert($user !== null);

        $token =  $this->accessTokenManager->create(
            $accessTokenEntity->getIdentifier(),
            $client,
            $user,
            null,
            $this->scopesToArray($accessTokenEntity->getScopes())
        );

        $event = new AccessTokenCreated($token->getId(), $user->getId(), (string)$client->getId());
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