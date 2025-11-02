<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'check-availability/*'
        ,
        'stripe/checkout',
        'stripe/success',
        'quotation/*',
        'cart/*',
        'api/user-behavior/*'



    ];

    protected function tokensMatch($request)
    {
        $token = $this->getTokenFromRequest($request);
        $sessionToken = $request->session()->token();

        return is_string($sessionToken) &&
               is_string($token) &&
               hash_equals($sessionToken, $token);
    }
} 