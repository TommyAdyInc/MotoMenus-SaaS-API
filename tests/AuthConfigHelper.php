<?php


namespace Tests;

use Illuminate\Contracts\Auth\Authenticatable;

class AuthConfigHelper
{
    /**
     * Get the user provider on configs.
     *
     * @param  Authenticatable $user
     * @return string|null
     */
    public static function getUserProvider(Authenticatable $user)
    {
        foreach (config('auth.providers') as $provider => $config) {
            if ($user instanceof $config['model']) {
                return $provider;
            }
        }

        throw new \Exception('No valid provider');
    }

    /**
     * Get the guard of specific provider to `passport` driver.
     *
     * @param  string $provider
     * @return string
     */
    public static function getProviderGuard($provider)
    {
        foreach (config('auth.guards') as $guard => $content) {
            if ($content['driver'] == 'passport' && $content['provider'] == $provider) {
                return $guard;
            }
        }

        throw new \Exception('No valid provider');
    }

    /**
     * Get the user guard on provider with `passport` driver.
     *
     * @param  Authenticatable $user
     * @return string|null
     */
    public static function getUserGuard(Authenticatable $user)
    {
        $provider = self::getUserProvider($user);

        return self::getProviderGuard($provider);
    }

    /**
     * @param string $provider
     * @return null|Illuminate\Database\Eloquent\Model
     */
    public static function getProviderModel($provider)
    {
        $model = config('auth.providers.'.$provider.'.model', null);

        return $model ? $model : null;
    }
}
