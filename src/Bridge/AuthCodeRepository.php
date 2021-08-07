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
use Laravel\Passport\Bridge\AuthCode as AuthCodeEntity;
use LaravelDoctrine\Passport\Contracts\Manager\AuthCode as AuthCodeManager;
use LaravelDoctrine\Passport\Contracts\Manager\Client as ClientManager;
use LaravelDoctrine\Passport\Contracts\Manager\User as UserManager;
use League\OAuth2\Server\Entities\AuthCodeEntityInterface;
use League\OAuth2\Server\Repositories\AuthCodeRepositoryInterface;

class AuthCodeRepository implements AuthCodeRepositoryInterface
{
    use ScopeConverter;
    protected AuthCodeManager $authCodeManager;
    protected UserManager $userManager;
    protected ClientManager $clientManager;
    protected Dispatcher $dispatcher;

    /**
     * @param AuthCodeManager $authCodeManager
     * @param UserManager     $userManager
     * @param ClientManager   $clientManager
     * @param Dispatcher      $dispatcher
     */
    public function __construct(AuthCodeManager $authCodeManager, UserManager $userManager, ClientManager $clientManager, Dispatcher $dispatcher)
    {
        $this->authCodeManager = $authCodeManager;
        $this->userManager     = $userManager;
        $this->clientManager   = $clientManager;
        $this->dispatcher      = $dispatcher;
    }

    /**
     * {@inheritDoc}
     */
    public function getNewAuthCode()
    {
        return new AuthCodeEntity();
    }

    /**
     * {@inheritDoc}
     */
    public function persistNewAuthCode(AuthCodeEntityInterface $authCodeEntity): void
    {
        $user   = $this->userManager->find($authCodeEntity->getUserIdentifier());
        $client = $this->clientManager->find($authCodeEntity->getClient()->getIdentifier());

        \assert(null !== $user);
        \assert(null !== $client);

        $this->authCodeManager->create(
            $authCodeEntity->getIdentifier(),
            $user,
            $client,
            $this->scopesToArray($authCodeEntity->getScopes()),
            $authCodeEntity->getExpiryDateTime()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function revokeAuthCode($codeId): void
    {
        $this->authCodeManager->revoke($codeId);
    }

    /**
     * {@inheritDoc}
     */
    public function isAuthCodeRevoked($codeId): bool
    {
        return $this->authCodeManager->isRevoked($codeId);
    }
}
