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
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait AccessTokenTrait
{
    use Timestamps;

    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id()
     */
    protected string $id;

    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\UserInterface")
     */
    protected ?User $user;

    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\ClientInterface"
     */
    protected Client $client;

    protected ?string $name;

    protected ?array $scopes = null;

    protected bool $revoked;

    public function revoke(): void
    {
        $this->revoked = true;
    }

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array|null
     */
    public function getScopes(): ?array
    {
        return $this->scopes;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }
}
