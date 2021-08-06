<?php

namespace LaravelDoctrine\Passport\Bridge;

use League\OAuth2\Server\Entities\ScopeEntityInterface;

trait ScopeConverter
{
    /**
     * Get an array of scope identifiers for storage.
     *
     * @param  array  $scopes
     * @return array
     */
    public function scopesToArray(array $scopes)
    {
        return array_map(function ($scope) {
            /** @var ScopeEntityInterface $scope */
            return $scope->getIdentifier();
        }, $scopes);
    }
}