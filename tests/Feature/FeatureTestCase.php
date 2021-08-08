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

use Laravel\Passport\PassportServiceProvider as LaravelPassportServiceProvider;
use LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use LaravelDoctrine\Passport\Providers\PassportServiceProvider;
use Mockery as m;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\LaravelDoctrine\Passport\Fixtures\TestServiceProvider;

class FeatureTestCase extends BaseTestCase
{
    /** {@inheritDoc} */
    protected function getPackageProviders($app): array
    {
        return [
            GedmoExtensionsServiceProvider::class,
            DoctrineServiceProvider::class,
            LaravelPassportServiceProvider::class,
            PassportServiceProvider::class,
            TestServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        m::close();
        parent::tearDown();
    }
}
