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

namespace Tests\LaravelDoctrine\Passport\Providers;

use Laravel\Passport\Passport;
use Laravel\Passport\PassportServiceProvider;
use LaravelDoctrine\Extensions\Timestamps\TimestampableExtension;
use LaravelDoctrine\Passport\Providers\LaravelDoctrinePassportServiceProvider;
use Tests\LaravelDoctrine\Passport\TestCase;
use LaravelDoctrine\Passport\Model;

/**
 * @covers \LaravelDoctrine\Passport\Providers\LaravelDoctrinePassportServiceProvider
 */
class LaravelDoctrinePassportServiceProviderTest extends TestCase
{
    public function test_providers_loaded()
    {
        $app = $this->app;
        $this->assertTrue($app->providerIsLoaded(PassportServiceProvider::class));
        $this->assertTrue($app->providerIsLoaded(LaravelDoctrinePassportServiceProvider::class));
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
}
