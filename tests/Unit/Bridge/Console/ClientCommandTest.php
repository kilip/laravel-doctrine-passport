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

namespace Tests\LaravelDoctrine\Passport\Unit\Bridge\Console;

use LaravelDoctrine\Passport\Testing\Concerns\InteractsWithUser;
use Tests\LaravelDoctrine\Passport\Feature\FeatureTestCase as TestCase;
use Tests\LaravelDoctrine\Passport\Feature\ORMTesting;

/**
 * @covers \LaravelDoctrine\Passport\Bridge\Console\ClientCommand
 */
class ClientCommandTest extends TestCase
{
    use InteractsWithUser;
    use ORMTesting;

    protected function setUp(): void
    {
        parent::setUp(); // TODO: Change the autogenerated stub
        $this->recreateDatabase();
    }

    public function test_it_should_create_personal_client()
    {
        $this->artisan('passport:client', ['--personal' => true])
            ->expectsQuestion('What should we name the personal access client?', '')
            ->assertExitCode(0)
            ->expectsOutput('Personal access client created successfully.');
    }

    public function test_it_should_create_password_client()
    {
        $this->artisan('passport:client', ['--password' => true])
            ->expectsQuestion('What should we name the password grant client?', '')
            ->expectsQuestion('Which user provider should this client use to retrieve users?', '')
            ->assertExitCode(0)
            ->expectsOutput('Password grant client created successfully.');
    }

    public function test_it_should_create_client_credentials()
    {
        $this->artisan('passport:client', ['--client' => true])
            ->expectsQuestion('What should we name the client?', '')
            ->expectsOutput('New client created successfully.')
            ->assertExitCode(0);
    }

    public function test_it_should_create_auth_code_client()
    {
        $user = $this->iHaveUser();
        $this->artisan('passport:client')
            ->expectsQuestion('Which user ID should the client be assigned to?', $user->getAuthIdentifier())
            ->expectsQuestion('What should we name the client?', 'test')
            ->expectsQuestion('Where should we redirect the request after authorization?', '')
            ->expectsOutput('New client created successfully.')
            ->assertExitCode(0);
    }
}