<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Test\Stubs;

use Anla\JWTAuth\Contracts\JWTSubject;

class UserStub implements JWTSubject
{
    public function getJWTIdentifier()
    {
        return 1;
    }

    public function getJWTCustomClaims()
    {
        return [
            'foo' => 'bar',
            'role' => 'admin',
        ];
    }
}
