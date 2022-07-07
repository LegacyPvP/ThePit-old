<?php
namespace Legacy\ThePit;

use Legacy\ThePit\Managers\CommandsManager;
use Legacy\ThePit\Managers\EventsManager;
use Legacy\ThePit\Managers\GradesManager;
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

    public function onEnable(): void
    {
        $this::setInstance($this);
        $this->saveResource("config.yml", true);

        ListenersManager::initListeners($this);
        CommandsManager::initCommands();
        EventsManager::initEvents();
        GradesManager::initGrades();
        LanguageManager::initLanguages();
        ScoreBoardManager::initScoreBoards();

        ItemFactory::getInstance()->register(new Snowball(new ItemIdentifier(ItemIds::SNOWBALL, 0), "Snowball"), true);
        ItemFactory::getInstance()->register(new Bow(new ItemIdentifier(ItemIds::BOW, 0), "Bow"), true);



    }
}