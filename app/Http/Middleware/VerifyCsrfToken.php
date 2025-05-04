<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [ 'api/tiktok/commit','api/tiktok/session/update','callback/tiktok/live',
        'api/service/2/desktop/device_register','/service/2/desktop/device_register','api/channel_fake/*','/api/callback/acb/transaction','/api/callback/acb/query'
        //
    ];
}
