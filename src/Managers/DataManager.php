<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Providers\BaseProvider;

abstract class DataManager
{
    /**
     * @param BaseProvider[] $providers
     */
    private static array $providers = [];

    public static function register(array $providers)
    {
        foreach ($providers as $provider) {
            if($provider instanceof BaseProvider)
            {
                self::$providers[$provider->getName()] = $provider;
            }
        }
    }
    public static function getProvider(string $name): BaseProvider
    {
        return self::$providers[$name];
    }

    public static function getProviders(): array
    {
        return self::$providers;
    }
}