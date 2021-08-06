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

trait IdentifiableTrait
{
    /**
     * @ORM\Column(type="bigint")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @var mixed|int|string|null
     */
    protected $id;

    /**
     * @return int|mixed|string|null
     */
    public function getId()
    {
        return $this->id;
    }
}
