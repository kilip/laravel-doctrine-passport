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

namespace Tests\LaravelDoctrine\Passport\Fixtures\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use LaravelDoctrine\ORM\Auth\Authenticatable;
use LaravelDoctrine\Passport\Contracts\Model as ModelContracts;
use LaravelDoctrine\Passport\Model\Traits\HasApiTokens;
use LaravelDoctrine\Passport\Model\Traits\IdentifiableTrait;

/**
 * @ORM\Entity
 * @ORM\Table(name="oauth_users")
 */
class User implements ModelContracts\User, ModelContracts\Identifiable
{
    use Authenticatable;
    use HasApiTokens;
    use IdentifiableTrait;

    /**
     * @ORM\Column(type="string")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="UUID")
     *
     * @var string
     */
    protected $id;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private string $username;

    /**
     * @ORM\Column(type="string")
     *
     * @var string
     */
    private string $email;

    public function __construct(string $username, string $email, string $password)
    {
        $this->username = $username;
        $this->email    = $email;
        $this->password = $password;
        $this->tokens   = new ArrayCollection();
        $this->clients  = new ArrayCollection();
    }

    /**
     * @return string
     */
    public function getUsername(): string
    {
        return $this->username;
    }

    /**
     * @return string
     */
    public function getEmail(): string
    {
        return $this->email;
    }

    /**
     * @return string
     */
    public function getPassword(): string
    {
        return $this->password;
    }
}
