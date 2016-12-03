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
use Anla\JWTAuth\Http\Middleware\Authenticate;
use Anla\JWTAuth\Http\Middleware\RefreshToken;
use Anla\JWTAuth\Http\Middleware\AuthenticateAndRenew;

class LaravelServiceProvider extends AbstractServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        $path = realpath(__DIR__.'/../../config/config.php');

        $this->publishes([$path => config_path('jwt.php')], 'config');
        $this->mergeConfigFrom($path, 'jwt');

        $this->app['router']->middleware('jwt.auth', Authenticate::class);
        $this->app['router']->middleware('jwt.refresh', RefreshToken::class);
        $this->app['router']->middleware('jwt.renew', AuthenticateAndRenew::class);
        $this->app['router']->middleware('jwt.check', Check::class);

        $this->extendAuthGuard();
    }
}
