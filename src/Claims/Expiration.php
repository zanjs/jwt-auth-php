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

use Anla\JWTAuth\Exceptions\TokenExpiredException;

class Expiration extends Claim
{
    use DatetimeTrait;

    /**
     * The claim name.
     *
     * @var string
     */
    protected $name = 'exp';

    /**
     * {@inheritdoc}
     */
    public function validatePayload()
    {
        if ($this->isPast($this->getValue())) {
            throw new TokenExpiredException('Token has expired');
        }
    }
}
