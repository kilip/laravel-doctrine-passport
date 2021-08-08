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

namespace LaravelDoctrine\Passport\Testing\Concerns;

use Faker;
use LaravelDoctrine\Passport\Contracts\Manager\ClientManager;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait InteractsWithClient
{
    protected function iHaveClientForUser(User $user, $provider = 'users'): Client
    {
        $faker = Faker\Factory::create();

        return $this->getClientManager()->create(
            $user,
            $faker->company,
            $faker->url,
            $provider,
            false,
            true
        );
    }

    protected function getClientManager(): ClientManager
    {
        return app(ClientManager::class);
    }
}
