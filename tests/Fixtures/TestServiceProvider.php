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

namespace Tests\LaravelDoctrine\Passport\Fixtures;

use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use Tests\LaravelDoctrine\Passport\Fixtures\Manager\UserManager;
use Tests\LaravelDoctrine\Passport\Fixtures\Model\User;
use function base_path;

class TestServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Passport::routes();
        Passport::ignoreMigrations();
        Passport::loadKeysFrom(__DIR__.'/Resources/keys');

        $this->loadRoutesFrom(__DIR__.'/Resources/routes/api.php');
    }

    public function register()
    {
        $this->configureDoctrine();
        $this->configureServices();
    }

    private function configureDoctrine(): void
    {
        /** @var ConfigRepository $config */
        $config = $this->app->make('config');

        $config->set('doctrine.managers.default.namespaces', [
            __NAMESPACE__.'\\Model',
        ]);
        $config->set('doctrine.managers.default.paths', [
            realpath(__DIR__.'/Model'),
        ]);

        if ( ! is_dir($dir = base_path('app/Entities'))) {
            mkdir($dir, 0777, true);
        }
        $config->set('doctrine.managers.default.proxies.path', base_path('storage/cache/doctrine-proxies'));

        if ( ! is_file($file=database_path('database.sqlite'))) {
            touch($file);
        }
        $config->set('doctrine_passport.models.user', User::class);
        $config->set('doctrine_passport.manager.user', UserManager::class);
        $config->set('auth.providers.users.driver', 'doctrine');
        $config->set('auth.providers.users.model', User::class);
        $config->set('auth.guards.api.driver', 'doctrine_passport');
    }

    /**
     * @psalm-suppress UndefinedInterfaceMethod
     */
    private function configureServices(): void
    {
        $app = $this->app;

        $app->when(UserManager::class)
            ->needs('$model')
            ->giveConfig('doctrine_passport.models.user');
    }
}
