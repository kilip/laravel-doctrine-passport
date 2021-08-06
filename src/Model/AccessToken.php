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

namespace LaravelDoctrine\Passport\Model;

use LaravelDoctrine\Passport\Contracts\Model\AccessToken as AccessTokenContract;
use LaravelDoctrine\Passport\Model\Traits\AccessTokenTrait;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class AccessToken implements AccessTokenContract
{
    use AccessTokenTrait;
}
