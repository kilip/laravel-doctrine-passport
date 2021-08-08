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

namespace LaravelDoctrine\Passport\Bridge\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Laravel\Passport\Http\Controllers\AuthorizedAccessTokenController as BaseAuthorizedAccessTokenController;
use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\RefreshTokenManager;

class AuthorizedAccessTokenController extends BaseAuthorizedAccessTokenController
{
    /**
     * @var AccessTokenManager
     */
    protected $tokenRepository;

    /**
     * @var RefreshTokenManager
     */
    protected $refreshTokenRepository;

    public function __construct(
        AccessTokenManager $accessTokenManager,
        RefreshTokenManager $refreshTokenManager
    ) {
        $this->tokenRepository        = $accessTokenManager;
        $this->refreshTokenRepository = $refreshTokenManager;
    }

    /**
     * @param Request $request
     *
     * @return Collection
     */
    public function forUser(Request $request)
    {
        /** @psalm-param  int|null|string $userId */
        $userId = $request->user()->getAuthIdentifier();

        return $this->tokenRepository
            ->findValidTokenForUser($userId)
            ->values();
    }
}
