<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Validators;

use Anla\JWTAuth\Claims\Collection;
use Anla\JWTAuth\Exceptions\TokenInvalidException;

class PayloadValidator extends Validator
{
    /**
     * @var array
     */
    protected $requiredClaims = [
        'iss',
        'iat',
        'exp',
        'nbf',
        'sub',
        'jti',
    ];

    /**
     * @var int
     */
    protected $refreshTTL = 20160;

    /**
     * Run the validations on the payload array.
     *
     * @param  \Anla\JWTAuth\Claims\Collection  $value
     *
     * @return void
     */
    public function check($value)
    {
        $this->validateStructure($value);

        return $this->refreshFlow ? $this->validateRefresh($value) : $this->validatePayload($value);
    }

    /**
     * Ensure the payload contains the required claims and
     * the claims have the relevant type.
     *
     * @param  \Anla\JWTAuth\Claims\Collection  $claims
     *
     * @throws \Anla\JWTAuth\Exceptions\TokenInvalidException
     *
     * @return \Anla\JWTAuth\Claims\Collection
     */
    protected function validateStructure(Collection $claims)
    {
        if (! $claims->hasAllClaims($this->requiredClaims)) {
            throw new TokenInvalidException('JWT payload does not contain the required claims');
        }
    }

    /**
     * Validate the payload timestamps.
     *
     * @param  \Anla\JWTAuth\Claims\Collection  $claims
     *
     * @throws \Anla\JWTAuth\Exceptions\TokenExpiredException
     * @throws \Anla\JWTAuth\Exceptions\TokenInvalidException
     *
     * @return \Anla\JWTAuth\Claims\Collection
     */
    protected function validatePayload(Collection $claims)
    {
        return $claims->validate('payload');
    }

    /**
     * Check the token in the refresh flow context.
     *
     * @param  \Anla\JWTAuth\Claims\Collection  $claims
     *
     * @throws \Anla\JWTAuth\Exceptions\TokenExpiredException
     *
     * @return \Anla\JWTAuth\Claims\Collection
     */
    protected function validateRefresh(Collection $claims)
    {
        return $this->refreshTTL === null ? $claims : $claims->validate('refresh', $this->refreshTTL);
    }

    /**
     * Set the required claims.
     *
     * @param  array  $claims
     *
     * @return $this
     */
    public function setRequiredClaims(array $claims)
    {
        $this->requiredClaims = $claims;

        return $this;
    }

    /**
     * Set the refresh ttl.
     *
     * @param  int  $ttl
     *
     * @return $this
     */
    public function setRefreshTTL($ttl)
    {
        $this->refreshTTL = $ttl;

        return $this;
    }
}
