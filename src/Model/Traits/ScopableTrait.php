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

trait ScopableTrait
{
    /**
     * @ORM\Column(type="array", nullable=true)
     * @psalm-param null|list<string>
     */
    protected ?array $scopes = null;

    /**
     * @return array|null
     */
    public function getScopes(): ?array
    {
        return $this->scopes;
    }
}
