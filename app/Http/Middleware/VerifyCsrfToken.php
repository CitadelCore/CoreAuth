<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as BaseVerifier;

class VerifyCsrfToken extends BaseVerifier
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array
     */
    protected $except = [
        // Disabling CSRF verification here since this is a SSO application...
        '/endpoints/login',
        '/endpoints/logout',
        '/endpoints/changepassword',
        '/endpoints/createaccount',
        '/endpoints/deleteaccount',
        '/endpoints/updateaccount',
        '/endpoints/api/login',
        '/endpoints/api/logout',
        '/endpoints/api/changepassword',
        '/endpoints/api/createaccount',
        '/endpoints/api/deleteaccount',
        '/endpoints/api/updateaccount',
    ];
}
