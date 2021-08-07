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

use LaravelDoctrine\Passport\Contracts\Model\AuthCode as AuthCodeModel;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;

interface AuthCode extends CanSaveObject
{
    /**
     * @param string             $identifier
     * @param UserModel          $user
     * @param ClientModel        $client
     * @param array              $scopes
     * @param \DateTimeInterface $expiry
     */
    public function create(
        string $identifier,
        UserModel $user,
        ClientModel $client,
        array $scopes,
        \DateTimeInterface $expiry
    ): void;

    /**
     * @param string $id
     *
     * @return AuthCodeModel|null
     */
    public function find(string $id): ?AuthCodeModel;

    /**
     * @param string $codeId
     *
     * @return bool
     */
    public function isRevoked(string $codeId): bool;

    /**
     * @param string $codeId
     */
    public function revoke(string $codeId): void;
}
