<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;
use Illuminate\Contracts\Encryption\Encrypter;
use Illuminate\Contracts\Foundation\Application;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [];

    public function __construct(Application $app, Encrypter $encrypter)
    {
        if( in_array(getenv('APP_ENV'), ['local', 'testing']) ){
            $this->except = ['*'];
        }
        parent::__construct($app, $encrypter);
    }

}
