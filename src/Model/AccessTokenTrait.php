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
    use HasClientTrait;
    use HasUserTrait;
    use IdentifiableTrait;
    use RevokableTrait;
    use ScopableTrait;
    use Timestamps;

    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id
     *
     * @var string|int|mixed|null
     */
    protected $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $name;

    public function __construct(
        string $id,
        Client $client,
        ?User $user,
        ?string $name,
        ?array $scopes,
        bool $revoked = false
    ) {
        $this->id      = $id;
        $this->client  = $client;
        $this->user    = $user;
        $this->name    = $name;
        $this->scopes  = $scopes;
        $this->revoked = $revoked;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
