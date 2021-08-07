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

namespace LaravelDoctrine\Passport\Exception;

class RuntimeException extends \Exception
{
    public static function createPersonalAccessClientNotFound(): self
    {
        return new self('Personal access client not found. Please create one.');
    }

    /**
     * @param int|string|null $userId
     *
     * @return self
     */
    public static function clientUserNotFoundException($userId): self
    {
        $userId = (string) $userId;

        return new self(sprintf(
            'Can\'t create client with user id: "%s".',
            $userId
        ));
    }

    public static function invalidRefreshTokenWithAccessTokenID(string $tokenId): self
    {
        return new self(sprintf(
            'Can\'t find refresh token with token id: "%s"',
            $tokenId
        ));
    }

    public static function authModelNotExist(): self
    {
        return new self('Unable to determine authentication model from configuration.');
    }
}
