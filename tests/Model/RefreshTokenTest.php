<?php

namespace Tests\LaravelDoctrine\Passport\Model;

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

