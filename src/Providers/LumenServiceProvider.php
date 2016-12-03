<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Providers;

use Anla\JWTAuth\Http\Middleware\Check;
use Anla\JWTAuth\Http\Parser\AuthHeaders;
use Anla\JWTAuth\Http\Parser\InputSource;
use Anla\JWTAuth\Http\Parser\QueryString;
use Anla\JWTAuth\Http\Middleware\Authenticate;
use Anla\JWTAuth\Http\Middleware\RefreshToken;
use Anla\JWTAuth\Http\Parser\LumenRouteParams;
use Anla\JWTAuth\Http\Middleware\AuthenticateAndRenew;

class LumenServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $this->app->configure('jwt');

        $path = realpath(__DIR__.'/../../config/config.php');
        $this->mergeConfigFrom($path, 'jwt');

        $this->app->routeMiddleware([
            'jwt.auth' => Authenticate::class,
            'jwt.refresh' => RefreshToken::class,
            'jwt.renew' => AuthenticateAndRenew::class,
            'jwt.check' => Check::class,
        ]);

        $this->extendAuthGuard();

        $this->app['anla.jwt.parser']->setChain([
            new AuthHeaders,
            new QueryString,
            new InputSource,
            new LumenRouteParams,
        ]);
    }
}
