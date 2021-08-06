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

namespace LaravelDoctrine\Passport\Contracts\Model;

interface Client extends Identifiable, Revokable, HasUser
{
    public function setName(string $name): void;

    public function setRedirect(string $redirect): void;

    public function setSecret(string $secret): void;

    /**
     * @return string
     */
    public function getName(): string;

    /**
     * @return string|null
     */
    public function getSecret(): ?string;

    public function getProvider(): ?string;

    /**
     * @return string
     */
    public function getRedirect(): string;

    /**
     * @return bool
     */
    public function isPersonalAccessClient(): bool;

    /**
     * @return bool
     */
    public function isPasswordClient(): bool;
}
