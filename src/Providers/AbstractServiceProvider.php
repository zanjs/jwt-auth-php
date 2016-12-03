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

use Anla\JWTAuth\JWT;
use Anla\JWTAuth\Factory;
use Anla\JWTAuth\JWTAuth;
use Anla\JWTAuth\Manager;
use Anla\JWTAuth\JWTGuard;
use Anla\JWTAuth\Blacklist;
use Anla\JWTAuth\Http\Parser\Parser;
use Illuminate\Support\ServiceProvider;
use Anla\JWTAuth\Http\Parser\AuthHeaders;
use Anla\JWTAuth\Http\Parser\InputSource;
use Anla\JWTAuth\Http\Parser\QueryString;
use Anla\JWTAuth\Http\Parser\RouteParams;
use Anla\JWTAuth\Contracts\Providers\Auth;
use Anla\JWTAuth\Contracts\Providers\Storage;
use Anla\JWTAuth\Validators\PayloadValidator;
use Anla\JWTAuth\Claims\Factory as ClaimFactory;
use Anla\JWTAuth\Console\JWTGenerateSecretCommand;
use Anla\JWTAuth\Contracts\Providers\JWT as JWTContract;

abstract class AbstractServiceProvider extends ServiceProvider
{
    /**
     * Boot the service provider.
     *
     * @return void
     */
    abstract public function boot();

    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register()
    {
        $this->registerAliases();

        $this->registerJWTProvider();
        $this->registerAuthProvider();
        $this->registerStorageProvider();
        $this->registerJWTBlacklist();

        $this->registerManager();
        $this->registerTokenParser();

        $this->registerJWT();
        $this->registerJWTAuth();
        $this->registerPayloadValidator();
        $this->registerClaimFactory();
        $this->registerPayloadFactory();
        $this->registerJWTCommand();

        $this->commands('anla.jwt.secret');
    }

    /**
     * Extend Laravel's Auth.
     *
     * @return void
     */
    protected function extendAuthGuard()
    {
        $this->app['auth']->extend('jwt', function ($app, $name, array $config) {
            $guard = new JwtGuard(
                $app['anla.jwt'],
                $app['auth']->createUserProvider($config['provider']),
                $app['request']
            );

            $app->refresh('request', $guard, 'setRequest');

            return $guard;
        });
    }

    /**
     * Bind some aliases.
     *
     * @return void
     */
    protected function registerAliases()
    {
        $this->app->alias('anla.jwt', JWT::class);
        $this->app->alias('anla.jwt.auth', JWTAuth::class);
        $this->app->alias('anla.jwt.provider.jwt', JWTContract::class);
        $this->app->alias('anla.jwt.provider.auth', Auth::class);
        $this->app->alias('anla.jwt.provider.storage', Storage::class);
        $this->app->alias('anla.jwt.manager', Manager::class);
        $this->app->alias('anla.jwt.blacklist', Blacklist::class);
        $this->app->alias('anla.jwt.payload.factory', Factory::class);
        $this->app->alias('anla.jwt.validators.payload', PayloadValidator::class);
    }

    /**
     * Register the bindings for the JSON Web Token provider.
     *
     * @return void
     */
    protected function registerJWTProvider()
    {
        $this->app->singleton('anla.jwt.provider.jwt', function ($app) {
            $provider = $this->config('providers.jwt');

            return $app->make(
                $provider,
                [$this->config('secret'), $this->config('algo'), $this->config('keys')]
            );
        });
    }

    /**
     * Register the bindings for the Auth provider.
     *
     * @return void
     */
    protected function registerAuthProvider()
    {
        $this->app->singleton('anla.jwt.provider.auth', function () {
            return $this->getConfigInstance('providers.auth');
        });
    }

    /**
     * Register the bindings for the Storage provider.
     *
     * @return void
     */
    protected function registerStorageProvider()
    {
        $this->app->singleton('anla.jwt.provider.storage', function () {
            return $this->getConfigInstance('providers.storage');
        });
    }

    /**
     * Register the bindings for the JWT Manager.
     *
     * @return void
     */
    protected function registerManager()
    {
        $this->app->singleton('anla.jwt.manager', function ($app) {
            $instance = new Manager(
                $app['anla.jwt.provider.jwt'],
                $app['anla.jwt.blacklist'],
                $app['anla.jwt.payload.factory']
            );

            return $instance->setBlacklistEnabled((bool) $this->config('blacklist_enabled'))
                            ->setPersistentClaims($this->config('persistent_claims'));
        });
    }

    /**
     * Register the bindings for the Token Parser.
     *
     * @return void
     */
    protected function registerTokenParser()
    {
        $this->app->singleton('anla.jwt.parser', function ($app) {
            $parser = new Parser(
                $app['request'],
                [new AuthHeaders, new QueryString, new InputSource, new RouteParams]
            );

            $app->refresh('request', $parser, 'setRequest');

            return $parser;
        });
    }

    /**
     * Register the bindings for the main JWT class.
     *
     * @return void
     */
    protected function registerJWT()
    {
        $this->app->singleton('anla.jwt', function ($app) {
            return new JWT(
                $app['anla.jwt.manager'],
                $app['anla.jwt.parser']
            );
        });
    }

    /**
     * Register the bindings for the main JWTAuth class.
     *
     * @return void
     */
    protected function registerJWTAuth()
    {
        $this->app->singleton('anla.jwt.auth', function ($app) {
            return new JWTAuth(
                $app['anla.jwt.manager'],
                $app['anla.jwt.provider.auth'],
                $app['anla.jwt.parser']
            );
        });
    }

    /**
     * Register the bindings for the Blacklist.
     *
     * @return void
     */
    protected function registerJWTBlacklist()
    {
        $this->app->singleton('anla.jwt.blacklist', function ($app) {
            $instance = new Blacklist($app['anla.jwt.provider.storage']);

            return $instance->setGracePeriod($this->config('blacklist_grace_period'))
                            ->setRefreshTTL($this->config('refresh_ttl'));
        });
    }

    /**
     * Register the bindings for the payload validator.
     *
     * @return void
     */
    protected function registerPayloadValidator()
    {
        $this->app->singleton('anla.jwt.validators.payload', function () {
            return (new PayloadValidator)
                ->setRefreshTTL($this->config('refresh_ttl'))
                ->setRequiredClaims($this->config('required_claims'));
        });
    }

    /**
     * Register the bindings for the Claim Factory.
     *
     * @return void
     */
    protected function registerClaimFactory()
    {
        $this->app->singleton('anla.jwt.claim.factory', function ($app) {
            $factory = new ClaimFactory($app['request']);
            $app->refresh('request', $factory, 'setRequest');

            return $factory->setTTL($this->config('ttl'));
        });
    }

    /**
     * Register the bindings for the Payload Factory.
     *
     * @return void
     */
    protected function registerPayloadFactory()
    {
        $this->app->singleton('anla.jwt.payload.factory', function ($app) {
            return new Factory(
                $app['anla.jwt.claim.factory'],
                $app['anla.jwt.validators.payload']
            );
        });
    }

    /**
     * Register the Artisan command.
     *
     * @return void
     */
    protected function registerJWTCommand()
    {
        $this->app->singleton('anla.jwt.secret', function () {
            return new JWTGenerateSecretCommand;
        });
    }

    /**
     * Helper to get the config values.
     *
     * @param  string  $key
     * @param  string  $default
     *
     * @return mixed
     */
    protected function config($key, $default = null)
    {
        return config("jwt.$key", $default);
    }

    /**
     * Get an instantiable configuration instance.
     *
     * @param  string  $key
     *
     * @return mixed
     */
    protected function getConfigInstance($key)
    {
        $instance = $this->config($key);

        if (is_string($instance)) {
            return $this->app->make($instance);
        }

        return $instance;
    }
}
