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

namespace Tests\LaravelDoctrine\Passport\Feature;

use LaravelDoctrine\Passport\Testing\Concerns\InteractsWithClient;
use LaravelDoctrine\Passport\Testing\Concerns\InteractsWithUser;

class AccessTokenTest extends FeatureTestCase
{
    use InteractsWithClient;
    use InteractsWithUser;
    use ORMTesting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recreateDatabase();
    }

    public function test_getting_access_token_with_password_grant(): void
    {
        $user = $this->iHaveUser('test', 'email@test.org', 'test');

        $client = $this->iHaveClientForUser($user);

        $response = $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->getId(),
            'client_secret' => $client->getSecret(),
            'username' => 'test',
            'password' => 'test',
        ]);

        $response->assertOk();
        $response->assertHeader('pragma', 'no-cache');
        $response->assertHeader('cache-control', 'no-store, private');
        $response->assertHeader('content-type', 'application/json; charset=UTF-8');

        $decodedResponse = $response->decodeResponseJson()->json();

        $this->assertArrayHasKey('token_type', $decodedResponse);
        $this->assertArrayHasKey('expires_in', $decodedResponse);
        $this->assertArrayHasKey('access_token', $decodedResponse);
        $this->assertArrayHasKey('refresh_token', $decodedResponse);
        $this->assertSame('Bearer', $decodedResponse['token_type']);
        $expiresInSeconds = 31536000;
        $this->assertEqualsWithDelta($expiresInSeconds, $decodedResponse['expires_in'], 5);
    }
}
