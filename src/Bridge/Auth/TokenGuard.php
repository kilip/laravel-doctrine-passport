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

namespace LaravelDoctrine\Passport\Bridge\Auth;

use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Http\Request;
use Laravel\Passport\Guards\TokenGuard as BaseTokenGuard;
use Laravel\Passport\PassportUserProvider;
use LaravelDoctrine\Passport\Contracts\Manager\AccessTokenManager;
use LaravelDoctrine\Passport\Contracts\Manager\ClientManager;
use LaravelDoctrine\Passport\Contracts\Model\Client;
use League\OAuth2\Server\ResourceServer;

class TokenGuard extends BaseTokenGuard
{
    /**
     * The token repository instance.
     *
     * @var AccessTokenManager
     */
    protected $tokens;

    /**
     * The client repository instance.
     *
     * @var ClientManager
     */
    protected $clients;

    /**
     * Create a new token guard instance.
     *
     * @param ResourceServer|mixed $server
     * @param PassportUserProvider $provider
     * @param AccessTokenManager   $tokens
     * @param ClientManager        $clients
     * @param Encrypter            $encrypter
     *
     * @return void
     */
    public function __construct(
        $server,
        PassportUserProvider $provider,
        AccessTokenManager $tokens,
        ClientManager $clients,
        Encrypter $encrypter
    ) {
        $this->server    = $server;
        $this->tokens    = $tokens;
        $this->clients   = $clients;
        $this->provider  = $provider;
        $this->encrypter = $encrypter;
    }

    /**
     * Determine if the requested provider matches the client's provider.
     *
     * @param Request $request
     *
     * @return bool
     */
    protected function hasValidProvider(Request $request)
    {
        /** @var ?Client $client */
        $client = $this->client($request);

        if ($client && ! $client->getProvider()) {
            return true;
        }

        return $client && $client->getProvider() === $this->provider->getProviderName();
    }
}
