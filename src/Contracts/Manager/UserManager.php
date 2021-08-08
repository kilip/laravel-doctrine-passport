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
     * @param int|string|null $id
     *
     * @return UserModel|null
     */
    public function find($id): ?UserModel;

    /**
     * @param string $username
     * @param string $password
     *
     * @return UserModel|null
     */
    public function findAndValidateForPassport(string $username, string $password): ?UserModel;

    /**
     * @param string $username
     * @param string $email
     * @param string $password
     *
     * @return UserModel
     */
    public function create(string $username, string $email, string $password): UserModel;
}
