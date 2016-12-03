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

use Illuminate\Http\Request;
use Anla\JWTAuth\Claims\JwtId;
use Anla\JWTAuth\Claims\Custom;
use Anla\JWTAuth\Claims\Issuer;
use Anla\JWTAuth\Claims\Factory;
use Anla\JWTAuth\Claims\Subject;
use Anla\JWTAuth\Claims\IssuedAt;
use Anla\JWTAuth\Claims\NotBefore;
use Anla\JWTAuth\Claims\Expiration;
use Anla\JWTAuth\Test\Fixtures\Foo;
use Anla\JWTAuth\Test\AbstractTestCase;

class FactoryTest extends AbstractTestCase
{
    /**
     * @var \Anla\JWTAuth\Claims\Factory
     */
    protected $factory;

    public function setUp()
    {
        parent::setUp();

        $this->factory = new Factory(Request::create('/foo', 'GET'));
    }

    /** @test */
    public function it_should_get_a_defined_claim_instance_when_passing_a_name_and_value()
    {
        $this->assertInstanceOf(Subject::class, $this->factory->get('sub', 1));
        $this->assertInstanceOf(Issuer::class, $this->factory->get('iss', 'http://example.com'));
        $this->assertInstanceOf(Expiration::class, $this->factory->get('exp', $this->testNowTimestamp + 3600));
        $this->assertInstanceOf(NotBefore::class, $this->factory->get('nbf', $this->testNowTimestamp));
        $this->assertInstanceOf(IssuedAt::class, $this->factory->get('iat', $this->testNowTimestamp));
        $this->assertInstanceOf(JwtId::class, $this->factory->get('jti', 'foo'));
    }

    /** @test */
    public function it_should_get_a_custom_claim_instance_when_passing_a_non_defined_name_and_value()
    {
        $this->assertInstanceOf(Custom::class, $this->factory->get('foo', ['bar']));
    }

    /** @test */
    public function it_should_make_a_claim_instance_with_a_value()
    {
        $iat = $this->factory->make('iat');
        $this->assertSame($iat->getValue(), $this->testNowTimestamp);
        $this->assertInstanceOf(IssuedAt::class, $iat);

        $nbf = $this->factory->make('nbf');
        $this->assertSame($nbf->getValue(), $this->testNowTimestamp);
        $this->assertInstanceOf(NotBefore::class, $nbf);

        $iss = $this->factory->make('iss');
        $this->assertSame($iss->getValue(), 'http://localhost/foo');
        $this->assertInstanceOf(Issuer::class, $iss);

        $exp = $this->factory->make('exp');
        $this->assertSame($exp->getValue(), $this->testNowTimestamp + 3600);
        $this->assertInstanceOf(Expiration::class, $exp);

        $jti = $this->factory->make('jti');
        $this->assertInstanceOf(JwtId::class, $jti);
    }

    /** @test */
    public function it_should_extend_claim_factory_to_add_a_custom_claim()
    {
        $this->factory->extend('foo', Foo::class);

        $this->assertInstanceOf(Foo::class, $this->factory->get('foo', 'bar'));
    }
}
