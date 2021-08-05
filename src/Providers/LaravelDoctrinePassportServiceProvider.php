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

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;
use LaravelDoctrine\Extensions;
use LaravelDoctrine\Passport\Model\AccessToken;
use LaravelDoctrine\Passport\Model\AuthCode;

class LaravelDoctrinePassportServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Passport::$tokenModel    = AccessToken::class;
        Passport::$authCodeModel = AuthCode::class;
    }

    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__.'/../../config/doctrine-passport.php',
            'doctrine_passport'
        );
        $this->configureDoctrine();
    }

    private function configureDoctrine(): void
    {
        $existing = (array) config('doctrine');

        $config = array_merge_recursive($existing, [
            'extensions' => [
                Extensions\Timestamps\TimestampableExtension::class,
            ],
        ]);

        config([
            'doctrine' => $config,
        ]);
    }
}
