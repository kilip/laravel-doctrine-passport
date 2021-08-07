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

use Doctrine\ORM\EntityManagerInterface;
use Illuminate\Contracts\Events\Dispatcher;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use LaravelDoctrine\Passport\Contracts\Manager\Client as ClientManagerContract;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientContract;
use LaravelDoctrine\Passport\Contracts\Model\User as UserContract;
use LaravelDoctrine\Passport\Contracts\Manager\PersonalAccessClient as PersonalAccessClientManager;
use LaravelDoctrine\Passport\Events\ClientRemoved;
use LaravelDoctrine\Passport\Exception\RuntimeException;
use LaravelDoctrine\Passport\Model\Client;

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
     * @param EntityManagerInterface $em
     * @param PersonalAccessClientManager $pacManager
     * @param Dispatcher $dispatcher
     * @param string $model
     * @param int|string|null $personalAccessClientId
     * @param ?string $personalAccessClientSecret
     */
    public function __construct(
        EntityManagerInterface $em,
        PersonalAccessClientManager $pacManager,
        Dispatcher $dispatcher,
        string $model,
        $personalAccessClientId,
        ?string $personalAccessClientSecret
    ) {
        $this->em                         = $em;
        $this->class                      = $model;
        $this->personalAccessClientId     = $personalAccessClientId;
        $this->personalAccessClientSecret = $personalAccessClientSecret;
        $this->dispatcher                 = $dispatcher;
        $this->pacManager = $pacManager;
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

        $client = $this->getRepository()->findOneBy([], ['id' => 'DESC']);

        if (null === $client) {
            throw RuntimeException::createPersonalAccessClientNotFound();
        }

        return $client;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress InvalidStringClass
     */
    public function create($userId, $name, $redirect, $provider = null, $personalAccess = false, $password = false, $confidential = true)
    {
        $repo = $this->em->getRepository(UserContract::class);
        $user = $repo->find($userId);

        if (null === $user) {
            throw RuntimeException::clientUserNotFoundException($userId);
        }

        $class = $this->class;
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
     * @psalm-suppress LessSpecificReturnStatement
     */
    public function createPersonalAccessClient($userId, $name, $redirect)
    {
        $client = $this->create($userId, $name, $redirect, null, true);
        $this->pacManager->create($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     * @psalm-suppress LessSpecificReturnStatement
     * @psalm-suppress MoreSpecificReturnType
     */
    public function createPasswordGrantClient($userId, $name, $redirect, $provider = null)
    {
        return $this->create($userId, $name, $redirect, $provider, false, true);
    }

    /**
     * {@inheritDoc}
     */
    public function update(ClientContract $client, $name, $redirect)
    {
        $client->setName($name);
        $client->setRedirect($redirect);
        $this->save($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     */
    public function regenerateSecret(ClientContract $client)
    {
        $client->setSecret(Str::random(40));
        $this->save($client);

        return $client;
    }

    /**
     * {@inheritDoc}
     */
    public function revoked($id)
    {
        $client = $this->find($id);

        return null === $client || $client->isRevoked();
    }

    /**
     * {@inheritDoc}
     */
    public function delete(ClientContract $client)
    {
        $event      = new ClientRemoved($client);
        $em         = $this->em;
        $dispatcher = $this->dispatcher;

        $dispatcher->dispatch($event);

        $em->remove($client);
        $em->flush();
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
    public function getPersonalAccessClientSecret()
    {
        return $this->personalAccessClientSecret;
    }
}
