<?php

namespace Legacy\ThePit\Librairies\Voltage\Api\listener;

use pocketmine\event\Listener;
use pocketmine\event\player\PlayerQuitEvent;
use pocketmine\utils\SingletonTrait;
use Legacy\ThePit\Librairies\Voltage\Api\ScoreBoardApi;

class ScoreBoardListener implements Listener
{
    use SingletonTrait;

    private static ScoreBoardApi $pg;

    public function __construct(ScoreBoardApi $pg){
        self::$pg = $pg;
        $pg->getServer()->getPluginManager()->registerEvents($this,$pg);
    }

    public function getPlugin() : ScoreBoardApi {
        return self::$pg;
    }

    public function onQuit(PlayerQuitEvent $event) : void {
        $player = $event->getPlayer();
        foreach (ScoreBoardApi::getManager()->getAllScoreBoard() as $scoreBoard) {
            if ($scoreBoard->hasPlayer($player)) {
                $scoreBoard->removePlayer($player);
            }
        }
    }
}