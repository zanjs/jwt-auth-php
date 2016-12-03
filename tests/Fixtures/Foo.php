<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Test\Fixtures;

use Anla\JWTAuth\Claims\Claim;

class Foo extends Claim
{
    /**
     * {@inheritdoc}
     */
    protected $name = 'foo';
}
