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

namespace LaravelDoctrine\Passport\Contracts;

use LaravelDoctrine\Passport\Contracts\Model\AccessToken;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;

interface TokenValidator
{
    /**
     * Find a valid token for given user and client.
     *
     * @param User   $user
     * @param Client $client
     */
    public function findValidToken(User $user, Client $client): ?AccessToken;
}
