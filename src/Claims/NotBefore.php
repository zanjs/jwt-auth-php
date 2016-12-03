<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Claims;

use Anla\JWTAuth\Exceptions\InvalidClaimException;
use Anla\JWTAuth\Exceptions\TokenInvalidException;

class NotBefore extends Claim
{
    use DatetimeTrait;

    /**
     * The claim name.
     *
     * @var string
     */
    protected $name = 'nbf';

    /**
     * {@inheritdoc}
     */
    public function validateCreate($value)
    {
        if (! is_numeric($value) || $this->isFuture($value)) {
            throw new InvalidClaimException($this);
        }

        return $value;
    }

    /**
     * {@inheritdoc}
     */
    public function validatePayload()
    {
        if ($this->isFuture($this->getValue())) {
            throw new TokenInvalidException('Not Before (nbf) timestamp cannot be in the future');
        }
    }
}
