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
use LaravelDoctrine\Extensions\Timestamps\Timestamps;

trait AccessTokenTrait
{
    use HasClientTrait;
    use HasUserTrait;
    use RevokableTrait;
    use ScopableTrait;
    use Timestamps;

    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id
     */
    protected string $id;

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected ?string $name;

    /**
     * @return string
     */
    public function getId(): string
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }
}
