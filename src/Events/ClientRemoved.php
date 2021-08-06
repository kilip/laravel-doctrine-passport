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

namespace LaravelDoctrine\Passport\Events;

use LaravelDoctrine\Passport\Contracts\Model\Client as ClientContract;

class ClientRemoved
{
    private ClientContract $client;

    /**
     * @param ClientContract $client
     */
    public function __construct(ClientContract $client)
    {
        $this->client = $client;
    }

    /**
     * @return ClientContract
     */
    public function getClient(): ClientContract
    {
        return $this->client;
    }
}
