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

use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;

interface AuthCode
{
    public function create(
        string $getIdentifier,
        UserModel $user,
        ClientModel $client,
        array $scopesToArray,
        \DateTimeImmutable $getExpiryDateTime
    ): void;

    public function isRevoked(string $codeId): bool;

    public function revoke(string $codeId): void;
}
