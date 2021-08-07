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

return [
    'load_model' => true,
    'entity_manager_name' => 'default',
    'models' => [
        'user' => null,
        'access_token' => LaravelDoctrine\Passport\Model\AccessToken::class,
        'auth_code' => LaravelDoctrine\Passport\Model\AuthCode::class,
        'client' => LaravelDoctrine\Passport\Model\Client::class,
        'personal_access_client' => LaravelDoctrine\Passport\Model\PersonalAccessClient::class,
        'refresh_token' => LaravelDoctrine\Passport\Model\RefreshToken::class,
    ],
    'manager' => [
        'access_token' => LaravelDoctrine\Passport\Manager\AccessToken::class,
        'auth_code' => LaravelDoctrine\Passport\Manager\AuthCodeManager::class,
        'client' => LaravelDoctrine\Passport\Manager\ClientManager::class,
        'refresh_token' => LaravelDoctrine\Passport\Manager\RefreshToken::class,
        'personal_access_client' => LaravelDoctrine\Passport\Manager\PersonalAccessClientManager::class,
    ],
];
