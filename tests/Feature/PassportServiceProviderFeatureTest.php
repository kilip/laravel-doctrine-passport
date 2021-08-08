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

namespace Tests\LaravelDoctrine\Passport\Feature;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\ClassMetadata;
use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider as LaravelPassportServiceProvider;
use LaravelDoctrine\Extensions\Timestamps\TimestampableExtension;
use LaravelDoctrine\ORM\Facades\EntityManager;
use LaravelDoctrine\Passport\Contracts\Manager as ManagerContracts;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Manager as Managers;
use LaravelDoctrine\Passport\Model;
use LaravelDoctrine\Passport\Providers\PassportServiceProvider;

/**
 * @covers \LaravelDoctrine\Passport\Providers\PassportServiceProvider
 */
class PassportServiceProviderFeatureTest extends FeatureTestCase
{
    public function test_providers_loaded()
    {
        $app = $this->app;
        $this->assertTrue($app->providerIsLoaded(LaravelPassportServiceProvider::class));
        $this->assertTrue($app->providerIsLoaded(PassportServiceProvider::class));
    }

    public function test_should_configure_doctrine()
    {
        $extensions = config('doctrine.extensions');
        $this->assertContains(
            TimestampableExtension::class,
            $extensions
        );
    }

    public function test_should_override_passport_model()
    {
        $this->assertSame(
            Model\AccessToken::class,
            Passport::$tokenModel
        );
        $this->assertSame(
            Model\AuthCode::class,
            Passport::$authCodeModel
        );
    }

    /**
     * @dataProvider getModels
     */
    public function test_should_load_doctrine_model(string $model)
    {
        $this->assertInstanceOf(
            ClassMetadata::class,
            $this->app->make(EntityManagerInterface::class)->getClassMetadata($model)
        );
    }

    public function getModels(): array
    {
        return [
            [ModelContracts\AccessToken::class],
            [ModelContracts\AuthCode::class],
            [ModelContracts\Client::class],
            [ModelContracts\PersonalAccessClient::class],
            [ModelContracts\RefreshToken::class],
        ];
    }

    /**
     * @param string $abstract
     * @param string $concrete
     * @dataProvider getManagers
     */
    public function test_it_should_load_model_manager(string $abstract, string $concrete)
    {
        $this->assertInstanceOf(
            $concrete,
            app($abstract)
        );
    }

    public function getManagers(): array
    {
        return [
            [ManagerContracts\AccessTokenManager::class, Managers\AccessTokenManager::class],
            [ManagerContracts\AuthCodeManager::class, Managers\AuthCodeManagerManager::class],
            [ManagerContracts\PersonalAccessClientManager::class, Managers\PersonalAccessClientManager::class],
            [ManagerContracts\ClientManager::class, Managers\ClientManager::class],
            [ManagerContracts\RefreshTokenManager::class, Managers\RefreshTokenManager::class],
        ];
    }

    public function test_it_should_extends_laravel_passport_services()
    {
        $app     = $this->app;
        $extends = [
            'AccessTokenRepository',
            'AuthCodeRepository',
            'ClientRepository',
            'RefreshTokenRepository',
            'UserRepository',
        ];
        foreach ($extends as $className) {
            $this->assertInstanceOf(
                'LaravelDoctrine\\Passport\\Bridge\\'.$className,
                $app->make('Laravel\\Passport\\Bridge\\'.$className)
            );
        }
    }

    public function test_it_should_creates_foo()
    {
        $repo = EntityManager::getRepository(ModelContracts\AuthCode::class);

        $this->assertIsObject($repo);
    }
}
