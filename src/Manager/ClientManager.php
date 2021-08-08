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

namespace LaravelDoctrine\Passport\Manager;

use Doctrine\Persistence\ObjectManager;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelDoctrine\Passport\Contracts\Manager\ClientManager as ClientManagerContract;
use LaravelDoctrine\Passport\Contracts\Manager\PersonalAccessClientManager as PersonalAccessClientManager;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientContract;
use LaravelDoctrine\Passport\Events\ClientRemoved;
use LaravelDoctrine\Passport\Exception\RuntimeException;

class ClientManager implements ClientManagerContract
{
    use HasRepository;

    private PersonalAccessClientManager $pacManager;

    /**
     * @var int|string|null
     */
    private $personalAccessClientId;

    /**
     * @var string|null
     */
    private ?string $personalAccessClientSecret;

    private Dispatcher $dispatcher;

    /**
     * @param ObjectManager               $om
     * @param PersonalAccessClientManager $pacManager
     * @param Dispatcher                  $dispatcher
     * @param string                      $model
     * @param int|string|null             $personalAccessClientId
     * @param ?string                     $personalAccessClientSecret
     * @psalm-param class-string $model
     */
    public function __construct(
        ObjectManager $om,
        PersonalAccessClientManager $pacManager,
        Dispatcher $dispatcher,
        string $model,
        $personalAccessClientId,
        ?string $personalAccessClientSecret
    ) {
        $this->om                         = $om;
        $this->class                      = $model;
        $this->personalAccessClientId     = $personalAccessClientId;
        $this->personalAccessClientSecret = $personalAccessClientSecret;
        $this->dispatcher                 = $dispatcher;
        $this->pacManager                 = $pacManager;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function find($id)
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function findActive($id): ?ClientContract
    {
        // add custom client key name
        return $this->getRepository()->findOneBy([
            'id' => $id,
            'revoked' => false,
        ]);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedInferredReturnType
     */
    public function findForUser($clientId, $userId)
    {
        return $this->getRepository()->findOneBy([
            'id' => $clientId,
            'user' => $userId,
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function forUser($userId)
    {
        $clients =  $this->getRepository()->findBy(
            ['user' => $userId],
            ['name' => 'ASC']
        );

        return new Collection($clients);
    }

    /**
     * {@inheritDoc}
     */
    public function activeForUser($userId)
    {
        $clients = $this->getRepository()->findBy(
            ['user' => $userId, 'revoked' => false],
            ['name' => 'ASC']
        );

        return new Collection($clients);
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedReturnStatement
     * @psalm-suppress MixedAssignment
     * @psalm-suppress MixedInferredReturnType
     */
    public function personalAccessClient(): ?ClientContract
    {
        if (null !== $this->personalAccessClientId) {
            return $this->find($this->personalAccessClientId);
        }

        $client = $this->getRepository()->findOneBy([]);

        if (null === $client) {
            throw RuntimeException::createPersonalAccessClientNotFound();
        }

        return $client;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress InvalidStringClass
     */
    public function create($user, $name, $redirect, $provider = null, $personalAccess = false, $password = false, $confidential = true)
    {
        $class  = $this->class;
        $client = new $class(
            $user,
            $name,
            ($confidential || $personalAccess) ? Str::random(40) : null,
            $provider,
            $redirect,
            $personalAccess,
            $password,
            false
        );

        $this->save($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function createPersonalAccessClient($user, $name, $redirect)
    {
        $client = $this->create($user, $name, $redirect, null, true);
        $this->pacManager->create($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    public function createPasswordGrantClient($user, $name, $redirect, $provider = null): ClientContract
    {
        return $this->create($user, $name, $redirect, $provider, false, true);
    }

    /**
     * {@inheritDoc}
     */
    public function update(ClientContract $client, $name, $redirect): ClientContract
    {
        $client->setName($name);
        $client->setRedirect($redirect);
        $this->save($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateSecret(ClientContract $client): ClientContract
    {
        $client->setSecret(Str::random(40));
        $this->save($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     */
    public function revoked($id): bool
    {
        $client = $this->find($id);

        return null === $client || $client->isRevoked();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(ClientContract $client): void
    {
        $event      = new ClientRemoved($client);
        $om         = $this->om;
        $dispatcher = $this->dispatcher;

        $dispatcher->dispatch($event);

        $om->remove($client);
        $om->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getPersonalAccessClientId()
    {
        return $this->personalAccessClientId;
    }

    /**
     * {@inheritDoc}
     */
    public function getPersonalAccessClientSecret(): ?string
    {
        return $this->personalAccessClientSecret;
    }
}
