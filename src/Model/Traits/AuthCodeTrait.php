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

namespace LaravelDoctrine\Passport\Model\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AuthCodeTrait
{
    use ExpirableTrait;
    use HasClientTrait;
    use HasUserTrait;
    use IdentifiableTrait;
    use RevokableTrait;
    use ScopableTrait;

    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id
     *
     * @var string|int|mixed|null
     */
    protected $id;
}
