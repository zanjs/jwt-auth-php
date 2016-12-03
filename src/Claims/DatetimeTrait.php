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

use DateTimeInterface;
use Anla\JWTAuth\Support\Utils;
use Anla\JWTAuth\Exceptions\InvalidClaimException;

trait DatetimeTrait
{
    /**
     * Set the claim value, and call a validate method.
     *
     * @param  mixed  $value
     *
     * @throws \Anla\JWTAuth\Exceptions\InvalidClaimException
     *
     * @return $this
     */
    public function setValue($value)
    {
        if ($value instanceof DateTimeInterface) {
            $value = $value->getTimestamp();
        }

        return parent::setValue($value);
    }

    /**
     * {@inheritdoc}
     */
    public function validateCreate($value)
    {
        if (! is_numeric($value)) {
            throw new InvalidClaimException($this);
        }

        return $value;
    }

    /**
     * Determine whether the value is in the future.
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function isFuture($value)
    {
        return Utils::isFuture($value);
    }

    /**
     * Determine whether the value is in the past.
     *
     * @param  mixed  $value
     *
     * @return bool
     */
    protected function isPast($value)
    {
        return Utils::isPast($value);
    }
}
