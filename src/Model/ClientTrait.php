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
use LaravelDoctrine\Passport\Contracts\Model\User;

trait ClientTrait
{
    use HasUserTrait;
    use IdentifiableTrait;
    use RevokableTrait;

    /**
     * @ORM\Column(type="string")
     */
    protected string $name;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    protected ?string $secret;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $provider;

    /**
     * @ORM\Column(type="text")
     */
    protected string $redirect;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $personalAccessClient;

    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $passwordClient;

    public function __construct(
        User $user,
        string $name,
        string $secret,
        ?string $provider,
        ?string $redirect,
        bool $personalAccessClient = false,
        bool $passwordClient = false,
        bool $revoked = false
    ) {
        $this->user                 = $user;
        $this->name                 = $name;
        $this->secret               = $secret;
        $this->provider             = $provider;
        $this->redirect             = $redirect;
        $this->personalAccessClient = $personalAccessClient;
        $this->passwordClient       = $passwordClient;
        $this->revoked              = $revoked;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @return string|null
     */
    public function getSecret(): ?string
    {
        return $this->secret;
    }

    /**
     * @return string|null
     */
    public function getProvider(): ?string
    {
        return $this->provider;
    }

    /**
     * @return string
     */
    public function getRedirect(): string
    {
        return $this->redirect;
    }

    /**
     * @return bool
     */
    public function isPersonalAccessClient(): bool
    {
        return $this->personalAccessClient;
    }

    /**
     * @return bool
     */
    public function isPasswordClient(): bool
    {
        return $this->passwordClient;
    }
}
