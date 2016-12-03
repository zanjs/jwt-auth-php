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

class Subject extends Claim
{
    /**
     * The claim name.
     *
     * @var string
     */
    protected $name = 'sub';
}
