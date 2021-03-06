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

namespace LaravelDoctrine\Passport\Bridge;

use Laravel\Passport\Bridge\Client as ClientEntity;
use Laravel\Passport\Passport;
use LaravelDoctrine\Passport\Contracts\Manager\ClientManager as ClientManager;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use League\OAuth2\Server\Repositories\ClientRepositoryInterface;

class ClientRepository implements ClientRepositoryInterface
{
    private ClientManager $clientManager;

    public function __construct(
        ClientManager $clientManager
    ) {
        $this->clientManager = $clientManager;
    }

    /**
     * {@inheritDoc}
     */
    public function getClientEntity($clientIdentifier)
    {
        $record = $this->clientManager->findActive($clientIdentifier);

        if (null === $record) {
            return null;
        }

        /** @var string $id */
        $id = $record->getId();

        return new ClientEntity(
            $id,
            $record->getName(),
            $record->getRedirect(),
            $record->confidential(),
            $record->getProvider()
        );
    }

    /**
     * {@inheritDoc}
     */
    public function validateClient($clientIdentifier, $clientSecret, $grantType): bool
    {
        $record = $this->clientManager->findActive($clientIdentifier);

        if (null === $record || ! $this->handlesGrant($record, $grantType)) {
            return false;
        }

        $storedHash     = (string) $record->getSecret();
        $secretVerified = ! ('' === $storedHash) && $this->verifySecret((string) $clientSecret, $storedHash);

        return ! $record->confidential() || $secretVerified;
    }

    /**
     * Determine if the given client can handle the given grant type.
     *
     * @param ClientModel $record
     * @param ?string     $grantType
     *
     * @return bool
     */
    protected function handlesGrant(ClientModel $record, ?string $grantType): bool
    {
        if ( ! \in_array($grantType, $record->getGrantTypes(), true)) {
            return false;
        }

        switch ($grantType) {
            case 'authorization_code':
                return ! $record->firstParty();
            case 'personal_access':
                return $record->isPersonalAccessClient() && $record->confidential();
            case 'password':
                return $record->isPasswordClient();
            case 'client_credentials':
                return $record->confidential();
            default:
                return true;
        }
    }

    /**
     * Verify the client secret is valid.
     *
     * @param string $clientSecret
     * @param string $storedHash
     *
     * @return bool
     */
    protected function verifySecret(string $clientSecret, string $storedHash): bool
    {
        return Passport::$hashesClientSecrets
            ? password_verify($clientSecret, $storedHash)
            : hash_equals($storedHash, $clientSecret);
    }
}
