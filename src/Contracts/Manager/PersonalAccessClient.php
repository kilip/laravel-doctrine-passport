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

use LaravelDoctrine\Passport\Contracts\Model\Client as ClientModel;

interface PersonalAccessClient extends CanSaveObject
{
    /**
     * @param object|ClientModel $client
     */
    public function create($client): void;
}
