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

class Custom extends Claim
{
    /**
     * @param  string  $name
     * @param  mixed  $value
     *
     * @return void
     */
    public function __construct($name, $value)
    {
        parent::__construct($value);
        $this->setName($name);
    }
}
