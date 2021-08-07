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

namespace Tests\LaravelDoctrine\Passport\Unit\Manager;

use LaravelDoctrine\Passport\Manager\PersonalAccessClientManager;
use LaravelDoctrine\Passport\Model\PersonalAccessClient;
use Mockery as m;
use Tests\LaravelDoctrine\Passport\Unit\UnitTestCase;

/**
 * @covers \LaravelDoctrine\Passport\Manager\PersonalAccessClientManager
 */
class PersonalAccessClientManagerTest extends UnitTestCase
{
    use TestModelManager;

    private PersonalAccessClientManager $manager;

    public function configureManager(): void
    {
        $this->modelClass   = PersonalAccessClient::class;
        $this->managerClass = PersonalAccessClientManager::class;

        $this->manager = new PersonalAccessClientManager(
            $this->em,
            $this->modelClass
        );
    }

    protected function configureSaveMock(bool $usePacMock = true)
    {
        $em   = $this->em;
        $args = $usePacMock ? $this->pac : m::type(PersonalAccessClient::class);
        $em->shouldReceive('persist')
            ->once()
            ->with($args);
        $em->shouldReceive('flush')
            ->once();
    }

    public function test_it_should_creates_new_personal_access_token()
    {
        $client  = $this->client;
        $manager = $this->manager;

        $this->configureSaveMock(false);

        $manager->create($client);
    }
}
