<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Http\Middleware;

use Anla\JWTAuth\JWTAuth;
use Illuminate\Http\Request;
use Anla\JWTAuth\Exceptions\JWTException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

abstract class BaseMiddleware
{
    /**
     * @var \Anla\JWTAuth\JWTAuth
     */
    protected $auth;

    /**
     * Create a new BaseMiddleware instance.
     *
     * @param  \Anla\JWTAuth\JWTAuth  $auth
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Check the request for the presence of a token.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\BadRequestHttpException
     *
     * @return void
     */
    public function checkForToken(Request $request)
    {
        if (! $this->auth->parser()->setRequest($request)->hasToken()) {
            throw new BadRequestHttpException('Token not provided');
        }
    }

    /**
     * Attempt to authenticate a user via the token in the request.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @throws \Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException
     *
     * @return void
     */
    public function authenticate(Request $request)
    {
        $this->checkForToken($request);

        try {
            $this->auth->parseToken()->authenticate();
        } catch (JWTException $e) {
            throw new UnauthorizedHttpException('jwt-auth', $e->getMessage(), $e, $e->getCode());
        }
    }
}
