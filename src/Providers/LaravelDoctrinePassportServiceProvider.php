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

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use LaravelDoctrine\Extensions;
use LaravelDoctrine\Passport\Contracts\Manager as ManagerContracts;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Manager;
use LaravelDoctrine\Passport\Model;
use Psr\Container\ContainerInterface;

class LaravelDoctrinePassportServiceProvider extends ServiceProvider
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

        $this->configureServices();
    }

    private function configureModels(): void
    {
        Passport::$tokenModel                = Model\AccessToken::class;
        Passport::$authCodeModel             = Model\AuthCode::class;
        Passport::$clientModel               = Model\Client::class;
        Passport::$personalAccessClientModel = Model\PersonalAccessClient::class;
        Passport::$refreshTokenModel         = Model\RefreshToken::class;

        /** @var \Illuminate\Config\Repository $config */
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
     * @psalm-suppress InvalidStringClass
     * @psalm-suppress MixedAssignment
     * @psalm-suppress PossiblyInvalidCast
     * @psalm-suppress MissingReturnType
     */
    private function configureServices(): void
    {
        $app                = $this->app;
        $accessTokenManager = (string) config('doctrine_passport.access_token_manager_class', Manager\AccessTokenManager::class);
        $app->singleton(ManagerContracts\AccessTokenManager::class,
            function (ContainerInterface $container) use ($accessTokenManager) {
                $em = $container->get(EntityManagerInterface::class);

                return new $accessTokenManager($em, Passport::$tokenModel);
            });
    }
}
