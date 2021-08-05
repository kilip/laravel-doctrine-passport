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

use Laravel\Passport\PassportServiceProvider;
use Tests\LaravelDoctrine\Passport\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Providers\LaravelDoctrinePassportServiceProvider
 */
class LaravelDoctrinePassportServiceProviderTest extends TestCase
{
    public function test_providers_loaded()
    {
        $app = $this->app;
        $this->assertTrue($app->providerIsLoaded(PassportServiceProvider::class));
    }
}
