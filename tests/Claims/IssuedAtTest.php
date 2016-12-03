<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Test\Claims;

use Anla\JWTAuth\Claims\IssuedAt;
use Anla\JWTAuth\Test\AbstractTestCase;

class IssuedAtTest extends AbstractTestCase
{
    public function setUp()
    {
        parent::setUp();
    }

    /**
     * @test
     * @expectedException \Anla\JWTAuth\Exceptions\InvalidClaimException
     */
    public function it_should_throw_an_exception_when_passing_a_future_timestamp()
    {
        new IssuedAt($this->testNowTimestamp + 3600);
    }
}
