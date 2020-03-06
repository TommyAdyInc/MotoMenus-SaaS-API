<?php

namespace App\Providers;

use App\PassportAuthCode;
use App\PassportPersonalAccessClient;
use App\PassportToken;
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
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        Passport::routes();

        Passport::tokensExpireIn(now()->addDays(60));
        Passport::refreshTokensExpireIn(now()->addDays(60));

        if(request()->has('admin')) {
            Passport::useTokenModel(PassportToken::class);
        }
        Passport::useAuthCodeModel(PassportAuthCode::class);
        Passport::usePersonalAccessClientModel(PassportPersonalAccessClient::class);
    }
}
