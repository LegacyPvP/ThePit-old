<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\Librairies\Voltage\Api\module\types\ScoreBoardLine;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Tasks\ScoreBoardTask;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use Legacy\ThePit\Librairies\Voltage\Api\ScoreBoardApi;
use Legacy\ThePit\Librairies\Voltage\Api\module\ScoreBoard;

abstract class ScoreBoardManager
{
    public static array $scoreboards = [];

    public static function initScoreBoards(): void {
        ScoreBoardApi::loadManager();

        $scoreboards = Core::getInstance()->getConfig()->get("scoreboards");
        foreach ($scoreboards as $type => $scoreboard){
            if(!is_array($scoreboard)) continue;
            self::$scoreboards[$type] = [
                "id" => array_search($type, array_keys($scoreboards)) + 1,
                "scoreboard" => ($manager = ScoreBoardApi::getManager())?->getScoreBoard(
                    $manager?->createScoreBoard(array_search($type, array_keys($scoreboards)) + 1)
                )
            ];
            if(!isset(self::$scoreboards[$type])){
                Core::getInstance()->getLogger()->emergency("[SCOREBOARDS] Failed to load ScoreBoard: $type ... Retrying in 5 seconds");
                Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($type): void {
                    Core::getInstance()->getLogger()->notice("[SCOREBOARDS] Retrying to load ScoreBoard: $type");
                    self::initScoreBoards();
                }), 5*20);
            }
            else Core::getInstance()->getLogger()->notice("[SCOREBOARDS] ScoreBoard For: $type Loaded");
        }

        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreBoardTask(), Core::getInstance()->getConfig()->getNested("scoreboards.refresh-time", 20));
    }

    /**
     * @return array
     */
    public static function getScoreboards(): array
    {
        return self::$scoreboards;
    }

    public static function getTitle(string $type): string {
        $type = $type === EventsManager::TYPE_NONE ? "basic" : $type;
        return Core::getInstance()->getConfig()->getNested("scoreboards.$type.title", "Legacy");
    }

    public static function getLines(string $type): array {
        return Core::getInstance()->getConfig()->getNested("scoreboards.$type.lines", []);
    }

    public static function updateScoreboard(?ScoreBoard $scoreboard, string $type): void {
        foreach (Server::getInstance()->getOnlinePlayers() as $player){
            foreach (self::getScoreboards() as $scoreboard){
                $scoreboard["scoreboard"]->removePlayer($player);
            }
            $scoreboard = match ($type){
                EventsManager::TYPE_NONE => match ($player->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0) {
                    0 => self::$scoreboards["basic"]["scoreboard"],
                    default => self::$scoreboards["prestige"]["scoreboard"],
                },
                EventsManager::TYPE_DEATHMATCH => self::$scoreboards[EventsManager::TYPE_DEATHMATCH]["scoreboard"],
                EventsManager::TYPE_RAFFLE => self::$scoreboards[EventsManager::TYPE_RAFFLE]["scoreboard"],
                EventsManager::TYPE_SPIRE => self::$scoreboards[EventsManager::TYPE_SPIRE]["scoreboard"],
            };
            if($player->getPlayerProperties()->getNestedProperties("parameters.scoreboard") ?? true) {
                $scoreboard?->addPlayer($player);
            }
            $scoreboard = self::updateLines($scoreboard, $type, $player);
        }
        $scoreboard = self::updateTitle($scoreboard, $type);
        $scoreboard?->sendToAll();
    }

    public static function updateLines(ScoreBoard $scoreboard, string $type, Player|LegacyPlayer|null $player = null): ScoreBoard {
        if($type === EventsManager::TYPE_NONE) $type = ($player?->getPlayerProperties()?->getNestedProperties("stats.prestige") ?? 0) >= 1 ? "prestige" : "basic";
        foreach (array_slice(self::getLines($type), 0, 15) as $i => $line){
            foreach (self::getParameters($type, $player) as $parameter => $value){
                $line = str_replace($parameter, $value, $line);
            }
            $scoreboard->setLineToAll(new ScoreBoardLine($i + 1, $line));
        }
        return $scoreboard;
    }

    public static function getParameters(string $type, ?LegacyPlayer $player): array {
        return match($type){
            "basic" => [
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{or}" => $player?->getPlayerProperties()->getNestedProperties("stats.or") ?? 0,
                "{credits}" => $player?->getPlayerProperties()->getNestedProperties("stats.coins") ?? 0,
                "{etoiles}" => $player?->getPlayerProperties()->getNestedProperties("stats.etoiles") ?? 0,
                "{votecoins}" => $player?->getPlayerProperties()->getNestedProperties("stats.votecoins") ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "prestige" => [
                "{prestige}" => $player?->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0,
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{or}" => $player?->getPlayerProperties()->getNestedProperties("stats.or") ?? 0,
                "{credits}" => $player?->getPlayerProperties()->getNestedProperties("stats.credits") ?? 0,
                "{etoiles}" => $player?->getPlayerProperties()->getNestedProperties("stats.etoiles") ?? 0,
                "{votecoins}" => $player?->getPlayerProperties()->getNestedProperties("stats.votecoins") ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "deathmatch" => [
                "{timeleft}" => 0, // Pour l'instant je met tout 0 puisque l'event n'existe pas (cause de crash)
                "{kills}" => 0,
                "{position}" => 0,
                "{kills_blue}" => 0,
                "{kills_red}" => 0,
                "{prestige}" => $player?->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0,
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "raffle" => [
                "{timeleft}" => 0,
                "{tickets}" => 0,
                "{position}" => 0,
                "{cagnotte}" => 0,
                "{prestige}" => $player?->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0,
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "spire" => [
                "{timeleft}" => 0,
                "{stage}" => 0,
                "{kills}" => 0,
                "{prestige}" => $player?->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0,
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            default => []

        };
    }

    public static function updateTitle(?ScoreBoard $scoreboard, string $type): ?ScoreBoard {
        $scoreboard?->setDisplayName(self::getTitle($type));
        return $scoreboard;
    }
}