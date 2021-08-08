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

namespace Tests\LaravelDoctrine\Passport\Fixtures\Manager;

use Doctrine\Persistence\ObjectManager;
use Illuminate\Support\Facades\Hash;
use LaravelDoctrine\Passport\Contracts\Manager\UserManager as UserManagerContract;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;
use LaravelDoctrine\Passport\Manager\HasRepository;
use Tests\LaravelDoctrine\Passport\Fixtures\Model\User;

class UserManager implements UserManagerContract
{
    use HasRepository;

    public function __construct(
        ObjectManager $om,
        string $model
    ) {
        $this->om    = $om;
        $this->class = $model;
    }

    public function find($id): ?UserModel
    {
        return $this->getRepository()->find($id);
    }

    public function findAndValidateForPassport(string $username, string $password): ?UserModel
    {
        $user = $this->getRepository()->findOneBy(['username' => $username]);
        if (null !== $user) {
        }

        return $user;
    }

    public function create(string $username, string $email, string $password): UserModel
    {
        $password = Hash::make($password);
        $user     = new User($username, $email, $password);
        $this->save($user);

        return $user;
    }
}
