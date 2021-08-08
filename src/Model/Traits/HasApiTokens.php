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

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Passport\Contracts\Model\AccessToken;

trait HasApiTokens
{
    /**
     * @ORM\OneToMany(targetEntity="LaravelDoctrine\Passport\Contracts\Model\AccessToken", mappedBy="user")
     *
     * @var Collection
     */
    protected Collection $tokens;

    /**
     * @ORM\OneToMany(targetEntity="LaravelDoctrine\Passport\Contracts\Model\Client", mappedBy="user")
     *
     * @var Collection
     */
    protected Collection $clients;

    /**
     * @var ?AccessToken
     */
    protected ?AccessToken $accessToken;

    /**
     * @return Collection
     */
    public function clients(): Collection
    {
        return $this->clients;
    }

    /**
     * @return Collection
     */
    public function tokens(): Collection
    {
        return $this->tokens;
    }

    public function token(): ?AccessToken
    {
        return $this->accessToken;
    }

    public function tokenCan(string $scope): bool
    {
        return null !== $this->accessToken && $this->accessToken->can($scope);
    }

    /**
     * @param AccessToken $token
     *
     * @return $this
     */
    public function withAccessToken(AccessToken $token): self
    {
        $this->accessToken = $token;

        return $this;
    }

    /**
     * @return Collection
     */
    public function getTokens(): Collection
    {
        if (null === $this->tokens) {
            $this->tokens = new ArrayCollection();
        }

        return $this->tokens;
    }

    /**
     * @return Collection
     */
    public function getClients(): Collection
    {
        if (null === $this->clients) {
            $this->clients = new ArrayCollection();
        }

        return $this->clients;
    }
}
