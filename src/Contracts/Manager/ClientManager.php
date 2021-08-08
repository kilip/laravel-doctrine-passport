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

namespace LaravelDoctrine\Passport\Contracts\Manager;

use Illuminate\Support\Collection;
use Laravel\Passport\ClientRepository;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientContract;
use LaravelDoctrine\Passport\Contracts\Model\User;

/**
 * @see ClientRepository
 */
interface ClientManager extends CanSaveObject
{
    /**
     * Get a client by the given ID.
     *
     * @param int|string|null $id
     *
     * @return ClientContract|null
     */
    public function find($id);

    /**
     * Get an active client by the given ID.
     *
     * @param string $id
     *
     * @return ClientContract|null
     */
    public function findActive(string $id): ?ClientContract;

    /**
     * Get a client instance for the given ID and user ID.
     *
     * @param int   $clientId
     * @param mixed $userId
     *
     * @return ClientContract|null
     */
    public function findForUser($clientId, $userId);

    /**
     * Get the client instances for the given user ID.
     *
     * @param mixed $userId
     *
     * @return Collection
     */
    public function forUser($userId);

    /**
     * Get the active client instances for the given user ID.
     *
     * @param mixed $userId
     *
     * @return Collection
     */
    public function activeForUser($userId);

    /**
     * Get the personal access token client for the application.
     *
     * @throws \RuntimeException
     *
     * @return ClientContract|null
     */
    public function personalAccessClient(): ?ClientContract;

    /**
     * Store a new client.
     *
     * @param ?User       $user
     * @param string      $name
     * @param string      $redirect
     * @param string|null $provider
     * @param bool        $personalAccess
     * @param bool        $password
     * @param bool        $confidential
     *
     * @return ClientContract|object
     */
    public function create(?User $user, string $name, string $redirect, ?string $provider = null, bool $personalAccess = false, bool $password = false, bool $confidential = true);

    /**
     * Store a new personal access token client.
     *
     * @param ?User  $user
     * @param string $name
     * @param string $redirect
     *
     * @return ClientContract|object
     */
    public function createPersonalAccessClient(?User $user, string $name, string $redirect);

    /**
     * Store a new password grant client.
     *
     * @param ?User       $user
     * @param string      $name
     * @param string      $redirect
     * @param string|null $provider
     *
     * @return ClientContract
     */
    public function createPasswordGrantClient(?User $user, string $name, string $redirect, ?string $provider = null): ClientContract;

    /**
     * Update the given client.
     *
     * @param ClientContract $client
     * @param string         $name
     * @param string         $redirect
     *
     * @return ClientContract
     */
    public function update(ClientContract $client, string $name, string $redirect): ClientContract;

    /**
     * Regenerate the client secret.
     *
     * @param ClientContract $client
     *
     * @return ClientContract
     */
    public function regenerateSecret(ClientContract $client): ClientContract;

    /**
     * Determine if the given client is revoked.
     *
     * @param int|string $id
     *
     * @return bool
     */
    public function revoked($id): bool;

    /**
     * Delete the given client.
     *
     * @param ClientContract $client
     *
     * @return void
     */
    public function delete(ClientContract $client): void;

    /**
     * Get the personal access client id.
     *
     * @return int|string|null
     */
    public function getPersonalAccessClientId();

    /**
     * Get the personal access client secret.
     *
     * @return string|null
     */
    public function getPersonalAccessClientSecret(): ?string;
}
