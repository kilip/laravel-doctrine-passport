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

namespace LaravelDoctrine\Passport\Contracts\Manager;

use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;

interface UserManager
{
    /**
     * @param int|string|null $userIdentifier
     *
     * @return UserModel|null
     */
    public function find($userIdentifier): ?UserModel;

    /**
     * @param string $username
     * @param string $password
     *
     * @return UserModel|null
     */
    public function findAndValidateForPassport(string $username, string $password): ?UserModel;
}
