<?php

namespace Legacy\ThePit;

use Legacy\ThePit\databases\SQLDatabase;
use Legacy\ThePit\librairies\libasynql\AwaitGenerator\Await;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\perks\Perk;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\tasks\GoldSpawnTask;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Core extends PluginBase
{
    use SingletonTrait;

    public static string $filePath = "";
    public static array $cache = [];

    protected function onLoad(): void
    {
        $this::setInstance($this);
        $this->saveDefaultConfig();
        self::$filePath = $this->getFile();
        Managers::loadManagers();
    }

    public function onEnable(): void
    {
        date_default_timezone_set('Europe/Paris');

        $default = yaml_parse(file_get_contents($this->getFile() . "resources/" . "config.yml"));
        if (is_array($default)) $this->getConfig()->setDefaults($default);

        $this->getScheduler()->scheduleDelayedRepeatingTask(new GoldSpawnTask(), 20 * 60, 20);
        $this->saveResource("config.yml", $this->isInDevMode());

        Managers::initManagers();

        Perk::setup();
    }

    protected function onDisable(): void
    {
        Managers::DATA()->saveAll();
        foreach($this->getServer()->getOnlinePlayers() as $player) {
            if ($player instanceof LegacyPlayer) {
                $player->save();
            }
        }
    }

    public function isInDevMode(): bool
    {
        return $this->getConfig()->get("dev-mode", true);
    }

    /**
     * @return string
     * @internal
     */
    public static function getFilePath(): string
    {
        return self::$filePath;
    }
}