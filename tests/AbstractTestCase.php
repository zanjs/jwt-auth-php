<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Test;

use Carbon\Carbon;
use PHPUnit_Framework_TestCase;

abstract class AbstractTestCase extends PHPUnit_Framework_TestCase
{
    /**
     * @var int
     */
    protected $testNowTimestamp;

    public function setUp()
    {
        parent::setUp();

        $now = Carbon::now();

        Carbon::setTestNow($now);
        $this->testNowTimestamp = $now->getTimestamp();
    }

    public function tearDown()
    {
        Carbon::setTestNow();

        parent::tearDown();
    }
}
