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

namespace LaravelDoctrine\Passport\Model;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Passport\Contracts\Model\AuthCode as AuthCodeContracts;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;
use LaravelDoctrine\Passport\Model\Traits\AuthCodeTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="oauth_auth_codes")
 *
 * @psalm-suppress MissingConstructor
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AuthCode implements AuthCodeContracts
{
    use AuthCodeTrait;

    public function __construct(
        string $id,
        Client $client,
        \DateTimeInterface $expiresAt,
        ?User $user,
        ?array $scopes,
        bool $revoked = false
    ) {
        $this->id        = $id;
        $this->client    = $client;
        $this->expiresAt = $expiresAt;
        $this->user      = $user;
        $this->scopes    = $scopes;
        $this->revoked   = $revoked;
    }
}
