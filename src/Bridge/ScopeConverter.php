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

namespace LaravelDoctrine\Passport\Bridge;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait ScopeConverter
{
    /**
     * Get an array of scope identifiers for storage.
     *
     * @param array $scopes
     *
     * @return array
     */
    public function scopesToArray(array $scopes)
    {
        return array_map(function ($scope) {
            /* @var ScopeEntityInterface $scope */
            return $scope->getIdentifier();
        }, $scopes);
    }
}
