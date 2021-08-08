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

use LaravelDoctrine\Passport\Contracts\Model\PersonalAccessClient as PersonalAccessClientContract;
use LaravelDoctrine\Passport\Model\Traits\PersonalAccessClientTrait;

/**
 * @psalm-suppress PropertyNotSetInConstructor
 */
class PersonalAccessClient implements PersonalAccessClientContract
{
    use PersonalAccessClientTrait;
}
