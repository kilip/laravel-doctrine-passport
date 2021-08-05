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

trait AccessToken
{
    /**
     * @ORM\Column(type="string", length=100)
     * @ORM\Id()
     */
    protected string $id;

    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\UserInterface")
     */
    protected ?UserInterface $user;

    /**
     * @ORM\ManyToOne(targetEntity="LaravelDoctrine\Passport\Model\ClientInterface"
     */
    protected ClientInterface $client;

    protected ?string $name;

    protected ?array $scopes = null;

    protected bool $revoked;

}
