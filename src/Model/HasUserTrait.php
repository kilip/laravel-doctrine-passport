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

use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\Passport\Contracts\Model\User;

trait HasUserTrait
{
    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\UserInterface")
     */
    protected ?User $user;

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
