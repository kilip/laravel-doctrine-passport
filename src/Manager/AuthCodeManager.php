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
use LaravelDoctrine\Passport\Contracts\Manager\AuthCode as AuthCodeManagerContract;
use LaravelDoctrine\Passport\Contracts\Model\AuthCode as AuthCodeModel;
use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;
use LaravelDoctrine\Passport\Contracts\Model\User as UserModel;
use LaravelDoctrine\Passport\Model\AuthCode;

class AuthCodeManager implements AuthCodeManagerContract
{
    use HasRepository;

    public function __construct(
        EntityManagerInterface $em,
        string $model
    ) {
        $this->em    = $em;
        $this->class = $model;
    }

    /**
     * {@inheritDoc}
     *
     * @psalm-suppress MixedInferredReturnType
     * @psalm-suppress MixedReturnStatement
     */
    public function find(string $id): ?AuthCodeModel
    {
        return $this->getRepository()->find($id);
    }

    /**
     * {@inheritDoc}
     */
    public function create(string $identifier, UserModel $user, ClientModel $client, array $scopes, \DateTimeInterface $expiry): void
    {
        $code = new AuthCode($identifier, $client, $expiry, $user, $scopes);
        $this->save($code);
    }

    /**
     * {@inheritDoc}
     */
    public function isRevoked(string $codeId): bool
    {
        $code = $this->find($codeId);

        return null === $code ? true : $code->isRevoked();
    }

    /**
     * {@inheritDoc}
     */
    public function revoke(string $codeId): void
    {
        $code = $this->find($codeId);

        if (null !== $code) {
            $code->revoke();
            $this->save($code);
        }
    }
}
