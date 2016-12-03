<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth;

use Anla\JWTAuth\Support\RefreshFlow;
use Anla\JWTAuth\Support\CustomClaims;
use Anla\JWTAuth\Exceptions\JWTException;
use Anla\JWTAuth\Exceptions\TokenBlacklistedException;
use Anla\JWTAuth\Contracts\Providers\JWT as JWTContract;

class Manager
{
    use CustomClaims, RefreshFlow;

    /**
     * @var \Anla\JWTAuth\Contracts\Providers\JWT
     */
    protected $provider;

    /**
     * @var \Anla\JWTAuth\Blacklist
     */
    protected $blacklist;

    /**
     * @var \Anla\JWTAuth\Factory
     */
    protected $payloadFactory;

    /**
     * @var bool
     */
    protected $blacklistEnabled = true;

    /**
     * @var array
     */
    protected $persistentClaims = [];

    /**
     * @param  \Anla\JWTAuth\Contracts\Providers\JWT  $provider
     * @param  \Anla\JWTAuth\Blacklist  $blacklist
     * @param  \Anla\JWTAuth\Factory  $payloadFactory
     *
     * @return void
     */
    public function __construct(JWTContract $provider, Blacklist $blacklist, Factory $payloadFactory)
    {
        $this->provider = $provider;
        $this->blacklist = $blacklist;
        $this->payloadFactory = $payloadFactory;
    }

    /**
     * Encode a Payload and return the Token.
     *
     * @param  \Anla\JWTAuth\Payload  $payload
     *
     * @return \Anla\JWTAuth\Token
     */
    public function encode(Payload $payload)
    {
        $token = $this->provider->encode($payload->get());

        return new Token($token);
    }

    /**
     * Decode a Token and return the Payload.
     *
     * @param  \Anla\JWTAuth\Token  $token
     * @param  bool  $checkBlacklist
     *
     * @throws \Anla\JWTAuth\Exceptions\TokenBlacklistedException
     *
     * @return \Anla\JWTAuth\Payload
     */
    public function decode(Token $token, $checkBlacklist = true)
    {
        $payloadArray = $this->provider->decode($token->get());

        $payload = $this->payloadFactory
                        ->setRefreshFlow($this->refreshFlow)
                        ->customClaims($payloadArray)
                        ->make();

        if ($checkBlacklist && $this->blacklistEnabled && $this->blacklist->has($payload)) {
            throw new TokenBlacklistedException('The token has been blacklisted');
        }

        return $payload;
    }

    /**
     * Refresh a Token and return a new Token.
     *
     * @param  \Anla\JWTAuth\Token  $token
     * @param  bool  $forceForever
     * @param  bool  $resetClaims
     *
     * @return \Anla\JWTAuth\Token
     */
    public function refresh(Token $token, $forceForever = false, $resetClaims = false)
    {
        $this->setRefreshFlow();

        if ($this->blacklistEnabled) {
            // invalidate old token
            $this->invalidate($token, $forceForever);
        }

        $claims = $this->buildRefreshClaims($this->decode($token, false));

        // return the new token
        return $this->encode(
            $this->payloadFactory->customClaims($claims)->make($resetClaims)
        );
    }

    /**
     * Invalidate a Token by adding it to the blacklist.
     *
     * @param  \Anla\JWTAuth\Token  $token
     * @param  bool  $forceForever
     *
     * @throws \Anla\JWTAuth\Exceptions\JWTException
     *
     * @return bool
     */
    public function invalidate(Token $token, $forceForever = false)
    {
        if (! $this->blacklistEnabled) {
            throw new JWTException('You must have the blacklist enabled to invalidate a token.');
        }

        return call_user_func(
            [$this->blacklist, $forceForever ? 'addForever' : 'add'],
            $this->decode($token, false)
        );
    }

    /**
     * Build the claims to go into the refreshed token.
     *
     * @param  Payload $payload
     *
     * @return array
     */
    protected function buildRefreshClaims(Payload $payload)
    {
        // assign the payload values as variables for use later
        extract($payload->toArray());

        // persist the relevant claims
        return array_merge(
            $this->customClaims,
            compact($this->persistentClaims, 'sub', 'iat')
        );
    }

    /**
     * Get the Payload Factory instance.
     *
     * @return \Anla\JWTAuth\Factory
     */
    public function getPayloadFactory()
    {
        return $this->payloadFactory;
    }

    /**
     * Get the JWTProvider instance.
     *
     * @return \Anla\JWTAuth\Contracts\Providers\JWT
     */
    public function getJWTProvider()
    {
        return $this->provider;
    }

    /**
     * Get the Blacklist instance.
     *
     * @return \Anla\JWTAuth\Blacklist
     */
    public function getBlacklist()
    {
        return $this->blacklist;
    }

    /**
     * Set whether the blacklist is enabled.
     *
     * @param  bool  $enabled
     *
     * @return $this
     */
    public function setBlacklistEnabled($enabled)
    {
        $this->blacklistEnabled = $enabled;

        return $this;
    }

    /**
     * Set the claims to be persisted when refreshing a token.
     *
     * @param  array  $claims
     *
     * @return $this
     */
    public function setPersistentClaims(array $claims)
    {
        $this->persistentClaims = $claims;

        return $this;
    }
}
