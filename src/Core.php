<?php
namespace Legacy\ThePit;

use Legacy\ThePit\Managers\CommandsManager;
use Legacy\ThePit\Managers\CustomItemManager;
use Legacy\ThePit\Managers\EventsManager;
use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Managers\LanguageManager;
use Legacy\ThePit\Managers\ListenersManager;
use Legacy\ThePit\Managers\ScoreBoardManager;
use Legacy\ThePit\Test\Bow;
use Legacy\ThePit\Test\Snowball;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemIds;
use pocketmine\plugin\PluginBase;
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

        CustomItemManager::registerItems();
        ListenersManager::initListeners($this);
        CommandsManager::initCommands();
        EventsManager::initEvents();
        RanksManager::initRanks();
        LanguageManager::initLanguages();
        ScoreBoardManager::initScoreBoards();

        ItemFactory::getInstance()->register(new Snowball(new ItemIdentifier(ItemIds::SNOWBALL, 0), "Snowball"), true);
        ItemFactory::getInstance()->register(new Bow(new ItemIdentifier(ItemIds::BOW, 0), "Bow"), true);
    }
}