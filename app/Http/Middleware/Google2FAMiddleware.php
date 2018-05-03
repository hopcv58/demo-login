<?php
/**
 * Created by PhpStorm.
 * User: HP
 * Date: 3/17/2018
 * Time: 2:48 PM
 */
namespace App\Http\Middleware;

use App\Supports\Google2FAAuthenticator;
use Closure;

class Google2FAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $authenticator = app(Google2FAAuthenticator::class)->boot($request);

        if ($authenticator->isAuthenticated()) {
            return $next($request);
        }

        return $authenticator->makeRequestOneTimePasswordResponse();
    }
}