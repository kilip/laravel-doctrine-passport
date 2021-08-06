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

namespace LaravelDoctrine\Passport\Events;

use LaravelDoctrine\Passport\Contracts\Model\AccessToken as AccessTokenContract;
use LaravelDoctrine\Passport\Contracts\Model\RefreshToken as RefreshTokenContract;

class RefreshTokenCreated
{
    private RefreshTokenContract $refreshToken;
    private AccessTokenContract $accessToken;

    /**
     * @param RefreshTokenContract $refreshToken
     * @param AccessTokenContract  $accessToken
     */
    public function __construct(RefreshTokenContract $refreshToken, AccessTokenContract $accessToken)
    {
        $this->refreshToken = $refreshToken;
        $this->accessToken  = $accessToken;
    }

    /**
     * @return RefreshTokenContract
     */
    public function getRefreshToken(): RefreshTokenContract
    {
        return $this->refreshToken;
    }

    /**
     * @return AccessTokenContract
     */
    public function getAccessToken(): AccessTokenContract
    {
        return $this->accessToken;
    }
}
