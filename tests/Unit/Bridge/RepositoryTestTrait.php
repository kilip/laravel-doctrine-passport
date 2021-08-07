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

namespace Tests\LaravelDoctrine\Passport\Unit\Bridge;

use Illuminate\Events\Dispatcher;
use LaravelDoctrine\Passport\Contracts\Manager\AccessToken as AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\AuthCode as AuthCodeManager;
use LaravelDoctrine\Passport\Contracts\Manager\Client as ClientManager;
use LaravelDoctrine\Passport\Contracts\Manager\RefreshToken as RefreshTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\User as UserManager;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken as TokenModel;
use LaravelDoctrine\Passport\Contracts\Model\AuthCode as AuthCodeModel;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Mockery as m;

trait RepositoryTestTrait
{
    /**
     * @var AccessTokenManager|m\LegacyMockInterface|m\MockInterface
     */
    protected $tokenManager;

    /**
     * @var Dispatcher|m\LegacyMockInterface|m\MockInterface
     */
    protected $dispatcher;

    /**
     * @var ClientManager|m\LegacyMockInterface|m\MockInterface
     */
    protected $clientManager;
    /**
     * @var UserManager|m\LegacyMockInterface|m\MockInterface
     */
    protected $userManager;
    /**
     * @var AuthCodeManager|m\LegacyMockInterface|m\MockInterface
     */
    protected $authCodeManager;

    /**
     * @var RefreshTokenManager|m\LegacyMockInterface|m\MockInterface
     */
    private $refreshTokenManager;

    /**
     * @var ScopeEntityInterface|m\LegacyMockInterface|m\MockInterface
     */
    protected $scope;

    /**
     * @var UserModel|m\LegacyMockInterface|m\MockInterface
     */
    protected $user;

    /**
     * @var AuthCodeModel|m\LegacyMockInterface|m\MockInterface
     */
    protected $authCode;

    /**
     * @var ClientModel|m\LegacyMockInterface|m\MockInterface
     */
    protected $client;

    /**
     * @var TokenModel|m\LegacyMockInterface|m\MockInterface
     */
    protected $token;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user     = m::mock(UserModel::class);
        $this->authCode = m::mock(AuthCodeModel::class);
        $this->client   = m::mock(ClientModel::class);
        $this->token    = m::mock(TokenModel::class);

        $this->authCodeManager     = m::mock(AuthCodeManager::class);
        $this->tokenManager        = m::mock(AccessTokenManager::class);
        $this->clientManager       = m::mock(ClientManager::class);
        $this->userManager         = m::mock(UserManager::class);
        $this->refreshTokenManager = m::mock(RefreshTokenManager::class);

        $this->dispatcher      = m::mock(Dispatcher::class);

        $this->scope = m::mock(ScopeEntityInterface::class);
        $this->scope->allows('getIdentifier')
            ->andReturns('scope-id');

        $this->setupRepository();
    }

    abstract protected function setupRepository(): void;
}
