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

use October\Rain\Auth\Models\User as UserModel;

class OctoberStub extends UserModel
{
    public function getId()
    {
        return 123;
    }
}
