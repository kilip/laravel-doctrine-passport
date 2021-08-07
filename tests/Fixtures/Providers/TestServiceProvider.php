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

namespace Tests\LaravelDoctrine\Passport\Fixtures\Providers;

use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class TestServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Passport::routes();
    }
}
