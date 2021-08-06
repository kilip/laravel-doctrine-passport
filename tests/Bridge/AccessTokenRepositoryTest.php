<?php

namespace Tests\LaravelDoctrine\Passport\Bridge;

use Illuminate\Events\Dispatcher;
use Laravel\Passport\Bridge\AccessToken as AccessTokenEntity;
use Laravel\Passport\Bridge\Client as ClientEntity;
use Laravel\Passport\Events\AccessTokenCreated;
use LaravelDoctrine\Passport\Bridge\AccessTokenRepository;
use LaravelDoctrine\Passport\Contracts\Manager\AccessToken as AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\Client as ClientManager;
use LaravelDoctrine\Passport\Contracts\Manager\User as UserManager;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;
use LaravelDoctrine\Passport\Contracts\Manager\AccessToken as AccessTokenModel;
use League\OAuth2\Server\Entities\AccessTokenEntityInterface;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Entities\ScopeEntityInterface;
use Tests\LaravelDoctrine\Passport\TestCase;
use Mockery as m;

class AccessTokenRepositoryTest extends TestCase
{
    /**
     * @var AccessTokenManager|m\LegacyMockInterface|m\MockInterface
     */
    private $tokenManager;

    /**
     * @var Dispatcher|m\LegacyMockInterface|m\MockInterface
     */
    private $dispatcher;

    private AccessTokenRepository $repository;
    /**
     * @var ClientManager|m\LegacyMockInterface|m\MockInterface
     */
    private $clientManager;
    /**
     * @var UserManager|m\LegacyMockInterface|m\MockInterface
     */
    private $userManager;
    /**
     * @var ScopeEntityInterface|m\LegacyMockInterface|m\MockInterface
     */
    private $scope;

    protected function setUp(): void
    {
        parent::setUp();

        $this->tokenManager = m::mock(AccessTokenManager::class);
        $this->clientManager = m::mock(ClientManager::class);
        $this->userManager = m::mock(UserManager::class);
        $this->dispatcher = m::mock(Dispatcher::class);
        $this->scope = m::mock(ScopeEntityInterface::class);
        $this->repository = new AccessTokenRepository(
            $this->tokenManager,
            $this->clientManager,
            $this->userManager,
            $this->dispatcher
        );

        $this->scope->allows('getIdentifier')
            ->andReturns('scope-id');
    }

    public function test_it_should_create_new_token()
    {
        $clientEntity = m::mock(ClientEntityInterface::class);
        $this->assertIsObject($this->repository->getNewToken($clientEntity, [$this->scope], 'user-id'));
    }

    public function test_it_should_creates_new_access_token()
    {
        $tokenManager = $this->tokenManager;

        $tokenManager->shouldReceive('create')
            ->with(
                'id',
                'user-id',
                'client-id',
                ['scopes'],
                m::type(\DateTimeInterface::class),
                false,
            );
    }

    public function test_it_should_persist_new_access_token()
    {
        $repo = $this->repository;
        $client = m::mock(ClientModel::class);
        $user = m::mock(UserModel::class);
        $entity = $this->buildAccessTokenEntity();
        $token = m::mock(AccessToken::class);
        $scope = $this->scope;
        $entity->setIdentifier('test');

        $token->shouldReceive('getId')
            ->once()->andReturn('token-id');
        $user->shouldReceive('getId')->once()->andReturn('user-id');
        $client->shouldReceive('getId')->once()->andReturn('client-id');
        $this->clientManager->shouldReceive('find')
            ->with('client-id')
            ->once()->andReturn($client);
        $this->userManager->shouldReceive('find')
            ->with('user-id')
            ->once()->andReturn($user);
        $this->tokenManager->shouldReceive('create')
            ->withAnyArgs()->andReturn($token);

        $this->dispatcher->shouldReceive('dispatch')
            ->with(m::type(AccessTokenCreated::class));

        $repo->persistNewAccessToken($entity);
    }

    public function test_it_revoke_an_access_token()
    {
        $this->tokenManager->shouldReceive('revokeAccessToken')
            ->once()
            ->with('token-id');
        $this->repository->revokeAccessToken('token-id');
    }

    public function test_it_should_check_if_access_token_has_been_revoked()
    {
        $this->tokenManager->shouldReceive('isAccessTokenRevoked')
            ->once()->with('token-id')->andReturn(false);
        $this->assertFalse($this->repository->isAccessTokenRevoked('token-id'));
    }

    protected function buildAccessTokenEntity(): AccessTokenEntityInterface
    {
        $client = new ClientEntity('client-id', 'client', 'redirect');
        $scope = $this->scope;

        $entity =  new AccessTokenEntity('user-id', [$scope],$client);
        $entity->setIdentifier('token-id');
        return $entity;
    }

}
