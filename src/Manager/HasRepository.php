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

use Doctrine\Persistence\ObjectManager;
use Doctrine\Persistence\ObjectRepository;

trait HasRepository
{
    protected ObjectManager $om;

    /**
     * @var string
     * @psalm-param class-string
     */
    protected $class;

    /**
     * @param object $entity
     * @param bool   $andFlush
     */
    public function save(object $entity, bool $andFlush = true): void
    {
        $this->om->persist($entity);
        if ($andFlush) {
            $this->om->flush();
        }
    }

    /**
     * @return ObjectRepository the repository class
     */
    public function getRepository(): ObjectRepository
    {
        return $this->om->getRepository($this->class);
    }
}
