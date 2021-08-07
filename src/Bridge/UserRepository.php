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

namespace LaravelDoctrine\Passport\Bridge;

use Illuminate\Contracts\Hashing\Hasher;
use Laravel\Passport\Bridge\User as UserEntity;
use LaravelDoctrine\Passport\Contracts\Manager\UserManager as UserManager;
use League\OAuth2\Server\Entities\ClientEntityInterface;
use League\OAuth2\Server\Repositories\UserRepositoryInterface;

class UserRepository implements UserRepositoryInterface
{
    private UserManager $userManager;
    private Hasher $hasher;

    public function __construct(
        UserManager $userManager,
        Hasher $hasher
    ) {
        $this->userManager = $userManager;
        $this->hasher      = $hasher;
    }

    /**
     * {@inheritDoc}
     */
    public function getUserEntityByUserCredentials($username, $password, $grantType, ClientEntityInterface $clientEntity)
    {
        $manager    = $this->userManager;
        $userEntity = null;
        $user       =  $manager->findAndValidateForPassport($username, $password);
        if (null !== $user) {
            $userEntity = new UserEntity($user->getPassportUserId());
        }

        return $userEntity;
    }
}
