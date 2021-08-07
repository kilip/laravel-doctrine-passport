<?php

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