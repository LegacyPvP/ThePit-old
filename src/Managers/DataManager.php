<?php

namespace Legacy\ThePit\Managers;

use JsonException;
use Legacy\ThePit\Core;
use Legacy\ThePit\Providers\BaseProvider;
use Legacy\ThePit\Providers\YAMLProvider;

final class DataManager extends Managers
{
    /**
     * @param BaseProvider[] $providers
     */
    private array $providers = [];

    public function load(): void
    {
        $this->add(
            new YAMLProvider("config", Core::getFilePath() . "resources/" . "config.yml"),
        );
    }

    public function init(): void
    {
        $this->providers = Core::$cache["data"];
        unset(Core::$cache["data"]);
        foreach ($this->getAll() as $provider) {
            Core::getInstance()->getLogger()->notice("[DATA] Provider: " . $provider->getName() . " Loaded");
        }
    }

    public function add(BaseProvider ...$providers)
    {
        foreach ($providers as $provider) {
            Core::$cache["data"][$provider->getName()] = $provider;
        }
    }

    public function get(string $name): ?BaseProvider
    {
        return $this->providers[$name] ?? reset($this->providers) ?: null;
    }

    /**
     * @return BaseProvider[] $providers
     */
    public function getAll(): array
    {
        return $this->providers;
    }

    /**
     * @throws JsonException
     */
    public function saveAll(): void
    {
        foreach ($this->getAll() as $provider) {
            $data = $provider->dump();
            $provider->getConfig()->setAll($data);
            $provider->getConfig()->save();
        }
    }
}