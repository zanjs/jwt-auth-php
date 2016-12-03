<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Test\Providers\Auth;

use Mockery;
use Illuminate\Contracts\Auth\Guard;
use Anla\JWTAuth\Test\AbstractTestCase;
use Anla\JWTAuth\Providers\Auth\Illuminate as Auth;

class IlluminateTest extends AbstractTestCase
{
    /**
     * @var \Mockery\MockInterface|\Illuminate\Contracts\Auth\Guard
     */
    protected $authManager;

    /**
     * @var \Anla\JWTAuth\Providers\Auth\Illuminate
     */
    protected $auth;

    public function setUp()
    {
        parent::setUp();

        $this->authManager = Mockery::mock(Guard::class);
        $this->auth = new Auth($this->authManager);
    }

    public function tearDown()
    {
        Mockery::close();

        parent::tearDown();
    }

    /** @test */
    public function it_should_return_true_if_credentials_are_valid()
    {
        $this->authManager->shouldReceive('once')->once()->with(['email' => 'foo@bar.com', 'password' => 'foobar'])->andReturn(true);
        $this->assertTrue($this->auth->byCredentials(['email' => 'foo@bar.com', 'password' => 'foobar']));
    }

    /** @test */
    public function it_should_return_true_if_user_is_found()
    {
        $this->authManager->shouldReceive('onceUsingId')->once()->with(123)->andReturn(true);
        $this->assertTrue($this->auth->byId(123));
    }

    /** @test */
    public function it_should_return_false_if_user_is_not_found()
    {
        $this->authManager->shouldReceive('onceUsingId')->once()->with(123)->andReturn(false);
        $this->assertFalse($this->auth->byId(123));
    }

    /** @test */
    public function it_should_return_the_currently_authenticated_user()
    {
        $this->authManager->shouldReceive('user')->once()->andReturn((object) ['id' => 1]);
        $this->assertSame($this->auth->user()->id, 1);
    }
}
