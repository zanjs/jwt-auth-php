<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Providers\Auth;

use Anla\JWTAuth\Contracts\Providers\Auth;
use Cartalyst\Sentinel\Sentinel as SentinelAuth;

class Sentinel implements Auth
{
    /**
     * @var \Cartalyst\Sentinel\Sentinel
     */
    protected $sentinel;

    /**
     * @param  \Cartalyst\Sentinel\Sentinel  $sentinel
     *
     * @return void
     */
    public function __construct(SentinelAuth $sentinel)
    {
        $this->sentinel = $sentinel;
    }

    /**
     * Check a user's credentials.
     *
     * @param  array  $credentials
     *
     * @return mixed
     */
    public function byCredentials(array $credentials)
    {
        return $this->sentinel->stateless($credentials);
    }

    /**
     * Authenticate a user via the id.
     *
     * @param  mixed  $id
     *
     * @return bool
     */
    public function byId($id)
    {
        if ($user = $this->sentinel->getUserRepository()->findById($id)) {
            $this->sentinel->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \Cartalyst\Sentinel\Users\UserInterface
     */
    public function user()
    {
        return $this->sentinel->getUser();
    }
}
