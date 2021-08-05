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

trait RevokableTrait
{
    /**
     * @ORM\Column(type="boolean")
     */
    protected bool $revoked = false;

    public function revoke(): void
    {
        $this->revoked = true;
    }

    /**
     * @return bool
     */
    public function isRevoked(): bool
    {
        return $this->revoked;
    }
}
