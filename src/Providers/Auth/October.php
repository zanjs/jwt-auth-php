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
use October\Rain\Auth\Manager as OctoberAuth;

class October implements Auth
{
    /**
     * @var \October\Rain\Auth\Manager
     */
    protected $october;

    /**
     * @param  \October\Rain\Auth\Manager  $october
     *
     * @return void
     */
    public function __construct(OctoberAuth $october)
    {
        $this->october = $october;
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
        return $this->october->findUserByCredentials($credentials);
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
        if ($user = $this->october->findUserById($id)) {
            $this->october->setUser($user);

            return true;
        }

        return false;
    }

    /**
     * Get the currently authenticated user.
     *
     * @return \October\Rain\Auth\Models\User
     */
    public function user()
    {
        return $this->october->getUser();
    }
}
