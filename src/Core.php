<?php
namespace Legacy\ThePit;

use Legacy\ThePit\Managers\CommandsManager;
use Legacy\ThePit\Managers\CustomItemManager;
use Legacy\ThePit\Managers\EventsManager;
use Legacy\ThePit\Managers\FormsManager;
use Legacy\ThePit\Managers\ItemsManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Managers\ListenersManager;
use Legacy\ThePit\Managers\ScoreBoardManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Core extends PluginBase
{
    use SingletonTrait;

    public static string $filePath = "";

    protected function onLoad(): void
    {
        self::$filePath = $this->getFile();
        CustomItemManager::initCustomItems();
    }

    public function onEnable(): void
    {
        date_default_timezone_set('Europe/Paris');

        $this::setInstance($this);
        $this->saveResource("config.yml", $this->isInDevMode());

        ListenersManager::initListeners($this);
        CommandsManager::initCommands();
        EventsManager::initEvents();
        RanksManager::initRanks();
        LanguageManager::initLanguages();
        ScoreBoardManager::initScoreBoards();
        CustomItemManager::registerItems();
        ItemsManager::initItems();
        FormsManager::initForms();

        $default = yaml_parse(file_get_contents($this->getFile() . "resources/" . "config.yml"));
        if(is_array($default)) $this->getConfig()->setDefaults($default);
    }

    public function isInDevMode(): bool
    {
        return $this->getConfig()->get("dev-mode", false);
    }

    /**
     * @return string
     */
    public static function getFilePath(): string
    {
        return self::$filePath;
    }

    /*

    public function getConfigByName(string $name): Config
    {
        return new Config($this->getDataFolder() . $name . ".yml", Config::YAML);
    }

    */
}