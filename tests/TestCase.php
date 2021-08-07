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

namespace Tests\LaravelDoctrine\Passport;

use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use Laravel\Passport\PassportServiceProvider as LaravelPassportServiceProvider;
use LaravelDoctrine\Extensions\GedmoExtensionsServiceProvider;
use LaravelDoctrine\ORM\DoctrineServiceProvider;
use LaravelDoctrine\Passport\Providers\PassportServiceProvider;
use Mockery as m;
use Orchestra\Testbench\TestCase as BaseTestCase;
use Tests\LaravelDoctrine\Passport\Fixtures\Model\User;

class TestCase extends BaseTestCase
{
    protected function getPackageProviders($app)
    {
        return [
            GedmoExtensionsServiceProvider::class,
            DoctrineServiceProvider::class,
            LaravelPassportServiceProvider::class,
            PassportServiceProvider::class,
        ];
    }

    protected function tearDown(): void
    {
        if ($container = m::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }
        m::close();
        parent::tearDown();
    }

    /**
     * @param Application $app
     */
    protected function getEnvironmentSetUp($app)
    {
        /** @var Repository $config */
        $config = $app['config'];

        $config->set('doctrine.managers.default.mappings', array_merge(
            $config->get('doctrine.managers.default.mappings', []), [
                __NAMESPACE__.'\\Fixtures\\Model' => [
                    'type' => 'annotation',
                    'dir' => __DIR__.'/Fixtures/Model',
                ],
            ]
        ));
        $config->set('doctrine.managers.default.paths', []);
        $config->set('doctrine_passport.models.user', User::class);
    }
}
