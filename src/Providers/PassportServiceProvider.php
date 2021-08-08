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

namespace LaravelDoctrine\Passport\Providers;

use Doctrine\Persistence\ObjectManager;
use Illuminate\Auth\RequestGuard;
use Illuminate\Config\Repository;
use Illuminate\Contracts\Auth\Guard;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport as BasePassport;
use Laravel\Passport\Console\ClientCommand;
use Laravel\Passport\Passport;
use LaravelDoctrine\Extensions;
use LaravelDoctrine\Passport\Bridge as DoctrineBridge;
use LaravelDoctrine\Passport\Bridge\Auth\TokenGuard;
use LaravelDoctrine\Passport\Contracts\Manager as ManagerContracts;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Contracts\TokenValidator;
use LaravelDoctrine\Passport\Manager;
use LaravelDoctrine\Passport\Model;
use League\OAuth2\Server\ResourceServer;

class PassportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        if (config('doctrine_passport.load_model', true)) {
            $this->configureModels();
        }
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/doctrine_passport.php',
            'doctrine_passport'
        );

        $this->configureManagers();
        $this->configureExtends();
        $this->registerGuard();
    }

    private function configureModels(): void
    {
        Passport::$tokenModel                = Model\AccessToken::class;
        Passport::$authCodeModel             = Model\AuthCode::class;
        Passport::$clientModel               = Model\Client::class;
        Passport::$personalAccessClientModel = Model\PersonalAccessClient::class;
        Passport::$refreshTokenModel         = Model\RefreshToken::class;

        /** @var Repository $config */
        $config      = $this->app->make('config');

        $managerName = (string) $config->get('doctrine_passport.entity_manager_name', 'default');
        $mappingsKey = "doctrine.managers.{$managerName}.mappings";
        $resolveKey  = "doctrine.managers.{$managerName}.resolve_target_entities";

        $config->set($mappingsKey, array_merge_recursive((array) $config->get($mappingsKey, []), [
            'LaravelDoctrine\\Passport\\Model' => [
                'type' => 'xml',
                'dir' => realpath(__DIR__.'/../../config/mapping'),
            ],
        ]));

        $userModel = (string) $config->get('doctrine_passport.models.user');

        $config->set($resolveKey, array_merge_recursive((array) $config->get($resolveKey, []), [
            ModelContracts\User::class => $userModel,
            ModelContracts\AccessToken::class => Passport::$tokenModel,
            ModelContracts\AuthCode::class => Passport::$authCodeModel,
            ModelContracts\Client::class => Passport::$clientModel,
            ModelContracts\PersonalAccessClient::class => Passport::$personalAccessClientModel,
            ModelContracts\RefreshToken::class => Passport::$refreshTokenModel,
        ]));

        $config->set('doctrine.extensions', array_merge((array) $config->get('doctrine.extensions', []), [
            Extensions\Timestamps\TimestampableExtension::class,
        ]));

        $config->set('doctrine.gedmo.all_mappings', true);
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     * @psalm-suppress PossiblyInvalidCast
     * @psalm-suppress MissingClosureReturnType
     */
    private function configureManagers(): void
    {
        $app                = $this->app;

        $app->bind(ObjectManager::class, function (Application $app) {
            return $app->make('em');
        });

        $app->singleton(ManagerContracts\AccessTokenManager::class, (string) config('doctrine_passport.manager.access_token'));
        $app->when(Manager\AccessTokenManager::class)
            ->needs('$model')
            ->giveConfig('doctrine_passport.models.access_token');

        $app->singleton(ManagerContracts\AuthCodeManager::class, (string) config('doctrine_passport.manager.auth_code'));
        $app->when(Manager\AuthCodeManagerManager::class)
            ->needs('$model')
            ->giveConfig('doctrine_passport.models.auth_code');

        $app->singleton(ManagerContracts\ClientManager::class, (string) config('doctrine_passport.manager.client'));
        $app->when(Manager\ClientManager::class)
            ->needs('$model')
            ->giveConfig('doctrine_passport.models.client');
        $app->when(Manager\ClientManager::class)
            ->needs('$personalAccessClientId')
            ->giveConfig('passport.personal_access_client.id', null);
        $app->when(Manager\ClientManager::class)
            ->needs('$personalAccessClientSecret')
            ->giveConfig('passport.personal_access_client.secret', null);

        $app->singleton(ManagerContracts\PersonalAccessClientManager::class, (string) config('doctrine_passport.manager.personal_access_client'));
        $app->when(Manager\PersonalAccessClientManager::class)
            ->needs('$model')
            ->giveConfig('doctrine_passport.models.personal_access_client');

        $app->singleton(ManagerContracts\RefreshTokenManager::class, (string) config('doctrine_passport.manager.refresh_token'));
        $app->when(Manager\RefreshTokenManager::class)
            ->needs('$model')
            ->giveConfig('doctrine_passport.models.refresh_token');

        $this->app->bind(ManagerContracts\UserManager::class, function ($app) {
            /** @var Application $app */
            $userManager = (string) config('doctrine_passport.manager.user');
            \assert(class_exists($userManager));

            return $app->make($userManager);
        });

        $this->app->bind(TokenValidator::class, DoctrineBridge\ORM\TokenManager::class);
    }

    /**
     * @psalm-suppress MissingClosureReturnType
     * @psalm-suppress MissingClosureParamType
     * @psalm-suppress UnusedClosureParam
     */
    private function configureExtends(): void
    {
        $extends = [
            'AccessTokenRepository',
            'AuthCodeRepository',
            'ClientRepository',
            'RefreshTokenRepository',
            'UserRepository',
        ];

        foreach ($extends as $className) {
            $abstract = 'Laravel\\Passport\\Bridge\\'.$className;
            $concrete = 'LaravelDoctrine\\Passport\\Bridge\\'.$className;
            $this->app->extend($abstract, function ($service, Application $app) use ($concrete) {
                return $app->make($concrete);
            });
        }

        $this->app->extend(ClientCommand::class, function ($service, Application $app) {
            return $app->make(DoctrineBridge\Console\ClientCommand::class);
        });
    }

    /**
     * Register the token guard.
     *
     * @psalm-suppress MixedMethodCall
     * @psalm-suppress PossiblyUndefinedMethod
     * @psalm-suppress MissingClosureReturnType
     * @psalm-suppress UnusedClosureParam
     * @psalm-suppress MixedAssignment
     * @psalm-suppress PossiblyInvalidCast
     */
    protected function registerGuard(): void
    {
        Auth::resolved(function ($auth) {
            $name = (string) config('doctrine_passport.guard_driver_name', 'doctrine_passport');
            $auth->extend($name, function (Application $app, string $name, array $config) {
                return tap($this->makeGuard($config), function (Guard $guard) {
                    app()->refresh('request', $guard, 'setRequest');
                });
            });
        });
    }

    /**
     * Make an instance of the token guard.
     *
     * @param array $config
     *
     * @return RequestGuard
     * @psalm-suppress MixedArgument
     * @psalm-suppress UndefinedInterfaceMethod
     * @psalm-suppress PossiblyNullArgument
     * @psalm-suppress MissingClosureParamType
     * @psalm-suppress MissingClosureReturnType
     */
    protected function makeGuard(array $config): RequestGuard
    {
        return new RequestGuard(function ($request) use ($config) {
            return (new TokenGuard(
                $this->app->make(ResourceServer::class),
                new BasePassport\PassportUserProvider(Auth::createUserProvider($config['provider']), $config['provider']),
                $this->app->make(ManagerContracts\AccessTokenManager::class),
                $this->app->make(ManagerContracts\ClientManager::class),
                $this->app->make('encrypter')
            ))->user($request);
        }, $this->app['request']);
    }
}
