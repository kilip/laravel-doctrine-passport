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

use Illuminate\Contracts\Validation\Factory as ValidationFactory;
use Illuminate\Http\Request;
use Laravel\Passport\Http\Controllers\ClientController as BaseClientController;
use Laravel\Passport\Http\Rules\RedirectRule;
use Laravel\Passport\Passport;
use LaravelDoctrine\Passport\Contracts\Manager\ClientManager;
use LaravelDoctrine\Passport\Contracts\Model\Client;

class ClientController extends BaseClientController
{
    /**
     * @var ClientManager
     */
    protected $clients;

    public function __construct(
        ClientManager $clients,
        ValidationFactory $validation,
        RedirectRule $redirectRule
    ) {
        $this->clients      = $clients;
        $this->validation   = $validation;
        $this->redirectRule = $redirectRule;
    }

    /**
     * Store a new client.
     *
     * @param \Illuminate\Http\Request $request
     *
     * @return object|Client|array
     */
    public function store(Request $request)
    {
        $this->validation->make($request->all(), [
            'name' => 'required|max:191',
            'redirect' => ['required', $this->redirectRule],
            'confidential' => 'boolean',
        ])->validate();

        $client = $this->clients->create(
            $request->user()->getAuthIdentifier(),
            $request->name,
            $request->redirect,
            null,
            false,
            false,
            (bool) $request->input('confidential', true)
        );

        if (Passport::$hashesClientSecrets) {
            return ['plainSecret' => $client->plainSecret] + $client->toArray();
        }

        return $client;
    }
}
