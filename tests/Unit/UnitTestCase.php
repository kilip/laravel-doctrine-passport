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

namespace Tests\LaravelDoctrine\Passport\Unit;

use Mockery as m;
use PHPUnit\Framework\TestCase as BaseTestCase;

class UnitTestCase extends BaseTestCase
{
    protected function tearDown(): void
    {
        if ($container = m::getContainer()) {
            $this->addToAssertionCount($container->mockery_getExpectationCount());
        }
        m::close();
        parent::tearDown();
    }
}
