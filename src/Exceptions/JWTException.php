<?php

/*
 * This file is part of jwt-auth.
 *
 * (c) Anla sheng <anlasheng@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Anla\JWTAuth\Exceptions;

use Exception;

class JWTException extends Exception
{
    /**
     * @param  string  $message
     * @param  int  $code
     * @param  \Exception|null  $previous
     *
     * @return void
     */
    public function __construct($message = 'An error occurred', $code = 0, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
