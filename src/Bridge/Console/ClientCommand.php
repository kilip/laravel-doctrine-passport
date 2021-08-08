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

namespace LaravelDoctrine\Passport\Bridge\Console;

use Illuminate\Console\Command;
use Laravel\Passport\Passport;
use LaravelDoctrine\Passport\Contracts\Manager\UserManager;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use LaravelDoctrine\Passport\Manager\ClientManager;

class ClientCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'passport:client
            {--personal : Create a personal access token client}
            {--password : Create a password grant client}
            {--client : Create a client credentials grant client}
            {--name= : The name of the client}
            {--provider= : The name of the user provider}
            {--redirect_uri= : The URI to redirect to after authorization }
            {--user_id= : The user ID the client should be assigned to }
            {--public : Create a public client (Auth code grant type only) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a client for issuing access tokens';

    public function handle(ClientManager $clientManager, UserManager $userManager): void
    {
        if ($this->option('personal')) {
            $this->createPersonalClient($clientManager);
        } elseif ($this->option('password')) {
            $this->createPasswordClient($clientManager);
        } elseif ($this->option('client')) {
            $this->createClientCredentialsClient($clientManager);
        } else {
            $this->createAuthCodeClient($clientManager, $userManager);
        }
    }

    /**
     * Create a new personal access client.
     *
     * @param ClientManager $clients
     *
     * @return void
     */
    protected function createPersonalClient(ClientManager $clients)
    {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the personal access client?',
            config('app.name').' Personal Access Client'
        );

        $client = $clients->createPersonalAccessClient(
            null, $name, 'http://localhost'
        );

        $this->info('Personal access client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Create a new password grant client.
     *
     * @param ClientManager $clients
     *
     * @return void
     */
    protected function createPasswordClient(ClientManager $clients)
    {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the password grant client?',
            config('app.name').' Password Grant Client'
        );

        $providers = array_keys(config('auth.providers'));

        $provider = $this->option('provider') ?: $this->choice(
            'Which user provider should this client use to retrieve users?',
            $providers,
            \in_array('users', $providers, true) ? 'users' : null
        );

        $client = $clients->createPasswordGrantClient(
            null, $name, 'http://localhost', $provider
        );

        $this->info('Password grant client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Create a client credentials grant client.
     *
     * @param ClientManager $clients
     *
     * @return void
     */
    protected function createClientCredentialsClient(ClientManager $clients)
    {
        $name = $this->option('name') ?: $this->ask(
            'What should we name the client?',
            config('app.name').' ClientCredentials Grant Client'
        );

        $client = $clients->create(
            null, $name, ''
        );

        $this->info('New client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Create a authorization code client.
     *
     * @param ClientManager $clients
     *
     * @return void
     */
    protected function createAuthCodeClient(ClientManager $clients, UserManager $users)
    {
        $userId = $this->option('user_id') ?: $this->ask(
            'Which user ID should the client be assigned to?'
        );

        $name = $this->option('name') ?: $this->ask(
            'What should we name the client?'
        );

        $redirect = $this->option('redirect_uri') ?: $this->ask(
            'Where should we redirect the request after authorization?',
            url('/auth/callback')
        );

        $user = $users->find($userId);

        $client = $clients->create(
            $user, $name, $redirect, null, false, false, ! $this->option('public')
        );

        $this->info('New client created successfully.');

        $this->outputClientDetails($client);
    }

    /**
     * Output the client's ID and secret key.
     *
     * @param Client $client
     *
     * @return void
     */
    protected function outputClientDetails(Client $client)
    {
        if (Passport::$hashesClientSecrets) {
            $this->line('<comment>Here is your new client secret. This is the only time it will be shown so don\'t lose it!</comment>');
            $this->line('');
        }

        $this->line('<comment>Client ID:</comment> '.$client->getId());
        $this->line('<comment>Client secret:</comment> '.$client->getSecret());
    }
}
