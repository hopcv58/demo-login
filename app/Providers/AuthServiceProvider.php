<?php

namespace App\Providers;

use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // Include passport routes.
        Passport::routes();

        // Set access_token expire in 5 minutes.
        Passport::tokensExpireIn(Carbon::now()->addMinutes(60*24*7));

        // Set refresh_token expire in 30 minutes.
        Passport::refreshTokensExpireIn(Carbon::now()->addMinutes(60*24*15));
    }
}
