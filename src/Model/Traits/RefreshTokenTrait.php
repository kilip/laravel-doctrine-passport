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
use LaravelDoctrine\Passport\Contracts\Model\AccessToken as AccessTokenContracts;

trait RefreshTokenTrait
{
    use ExpirableTrait;
    use IdentifiableTrait;
    use RevokableTrait;
    use Timestamps;

    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id
     *
     * @var string|int|mixed|null
     */
    protected $id;

    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Contracts\Model\AccessToken")
     */
    protected AccessTokenContracts $accessToken;

    public function __construct(
        string $id,
        AccessTokenContracts $accessToken,
        \DateTimeImmutable $expiresAt,
        bool $revoked = false
    )
    {
        $this->id = $id;
        $this->accessToken = $accessToken;
        $this->expiresAt = $expiresAt;
        $this->revoked = $revoked;
    }

    /**
     * @return AccessTokenContracts
     */
    public function getAccessToken(): AccessTokenContracts
    {
        return $this->accessToken;
    }
}
