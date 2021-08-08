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

use LaravelDoctrine\Passport\Contracts\Manager\UserManager;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait InteractsWithUser
{
    public function iHaveUser($username = 'test', $email='test@example.com', $password = 'test'): User
    {
        return $this->getUserManager()->create($username, $email, $password);
    }

    protected function getUserManager(): UserManager
    {
        return $this->app->make(UserManager::class);
    }
}
