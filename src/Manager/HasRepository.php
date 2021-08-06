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

namespace LaravelDoctrine\Passport\Manager;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;

trait HasRepository
{
    protected EntityManagerInterface $em;

    /**
     * @var string
     */
    protected string $class;

    /**
     * @return EntityRepository the repository class
     * @psalm-return EntityRepository
     * @psalm-suppress ArgumentTypeCoercion
     */
    public function getRepository()
    {
        return $this->em->getRepository($this->class);
    }
}
