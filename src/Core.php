<?php
namespace Legacy\ThePit;

use Legacy\ThePit\Managers\CommandsManager;
use Legacy\ThePit\Managers\CustomItemManager;
use Legacy\ThePit\Managers\EventsManager;
use Legacy\ThePit\Managers\ItemsManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Managers\ListenersManager;
use Legacy\ThePit\Managers\ScoreBoardManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use pocketmine\utils\SingletonTrait;

class Core extends PluginBase
{
    use SingletonTrait;

    protected function onLoad(): void
    {
        CustomItemManager::initCustomItems();
    }

    public function onEnable(): void
    {
        date_default_timezone_set('Europe/Paris');

        $this::setInstance($this);
        $this->saveResource("config.yml", true);
        if (!file_exists($this->getDataFolder()."knockback.yml")){
            $this->saveResource('knockback.yml');
        }

        ListenersManager::initListeners($this);
        CommandsManager::initCommands();
        EventsManager::initEvents();
        RanksManager::initRanks();
        LanguageManager::initLanguages();
        ScoreBoardManager::initScoreBoards();
        CustomItemManager::registerItems();
        ItemsManager::initItems();
    }

    public function getConfigByName(string $name): Config
    {
        return new Config($this->getDataFolder() . $name . ".yml", Config::YAML);
    }
}