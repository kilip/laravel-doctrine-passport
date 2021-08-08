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

use Illuminate\Support\Facades\Artisan;
use LaravelDoctrine\Passport\Testing\Concerns\InteractsWithClient;
use LaravelDoctrine\Passport\Testing\Concerns\InteractsWithUser;

/**
 * @covers \LaravelDoctrine\Passport\Bridge\Auth\TokenGuard
 */
class AuthorizationTest extends FeatureTestCase
{
    use InteractsWithClient;
    use InteractsWithUser;
    use ORMTesting;

    protected function setUp(): void
    {
        parent::setUp();
        $this->recreateDatabase();
        Artisan::call('passport:install');
    }

    public function test_it_should_authorize()
    {
        $user = $this->iHaveUser();

        $client = $this->iHaveClientForUser($user);

        $response = $this->post('/oauth/token', [
            'grant_type' => 'password',
            'client_id' => $client->getId(),
            'client_secret' => $client->getSecret(),
            'username' => 'test',
            'password' => 'test',
        ]);
        $response->assertOk();

        $json = $response->decodeResponseJson();

        $response = $this->get('/', [
            'Authorization' => $json['token_type'].' '.$json['access_token'],
        ]);

        $response->assertOk();
        $this->assertAuthenticated('api');
    }
}
