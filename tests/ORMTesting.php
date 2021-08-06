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

namespace Tests\LaravelDoctrine\Passport;

use Doctrine\ORM\Tools\SchemaTool;
use Doctrine\ORM\Tools\ToolsException;
use LaravelDoctrine\ORM\IlluminateRegistry;

trait ORMTesting
{
    protected function recreateDatabase()
    {
        $registry = $this->getIlluminateRegistry();

        foreach ($registry->getManagers() as $manager) {
            $meta = $manager->getMetadataFactory()->getAllMetadata();
            $tool = new SchemaTool($manager);
            try {
                $tool->dropSchema($meta);
                $tool->createSchema($meta);
            } catch (ToolsException $e) {
                throw new \InvalidArgumentException("Database schema is not buildable: {$e->getMessage()}", $e->getCode(), $e);
            }
        }
    }

    /**
     * @return IlluminateRegistry
     */
    protected function getIlluminateRegistry()
    {
        return app()->get('registry');
    }
}
