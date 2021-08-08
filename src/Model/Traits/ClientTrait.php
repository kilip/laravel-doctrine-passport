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

namespace LaravelDoctrine\Passport\Model\Traits;

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Extensions\Timestamps\Timestamps;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait ClientTrait
{
    use HasUserTrait;
    use IdentifiableTrait;
    use RevokableTrait;
    use Timestamps;

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

    /**
     * @var array
     */
    protected array $grantTypes = [];

    public function __construct(
        ?User $user,
        string $name,
        ?string $secret,
        ?string $provider,
        string $redirect,
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
        $this->grantTypes           = [
            'authorization_code',
            'personal_access',
            'password',
            'client_credentials',
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function confidential(): bool
    {
        return ! empty($this->secret);
    }

    /**
     * {@inheritDoc}
     */
    public function firstParty(): bool
    {
        return $this->isPersonalAccessClient() || $this->isPasswordClient();
    }

    /**
     * @return array
     */
    public function getGrantTypes(): array
    {
        return $this->grantTypes;
    }

    /**
     * {@inheritDoc}
     */
    public function setGrantTypes(array $grantTypes): void
    {
        $this->grantTypes = $grantTypes;
    }

    /**
     * @param string $name
     */
    public function setName(string $name): void
    {
        $this->name = $name;
    }

    /**
     * @param string|null $secret
     */
    public function setSecret(?string $secret): void
    {
        $this->secret = $secret;
    }

    /**
     * @param string $redirect
     */
    public function setRedirect(string $redirect): void
    {
        $this->redirect = $redirect;
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
