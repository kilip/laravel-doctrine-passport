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

namespace Tests\LaravelDoctrine\Passport\Unit\Model;

use LaravelDoctrine\Passport\Contracts\Model\AccessToken;
use PHPUnit\Framework\TestCase;

/**
 * @covers \LaravelDoctrine\Passport\Model\RefreshToken
 */
class RefreshTokenTest extends TestCase
{
    use TestModelProperties;

    /**
     * @var AccessToken|mixed|\PHPUnit\Framework\MockObject\MockObject
     */
    private $token;

    protected function setUp(): void
    {
        $this->token = $this->createMock(AccessToken::class);

        $this->model = new \LaravelDoctrine\Passport\Model\RefreshToken(
            'identifier',
            $this->token,
            new \DateTimeImmutable(),
            true
        );
    }

    public function getTestMutableProperties(): array
    {
        return [
            ['accessToken', AccessToken::class],
        ];
    }
}
