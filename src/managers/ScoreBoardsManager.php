<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\events\Event;
use Legacy\ThePit\scoreboard\module\types\ScoreBoardLine;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\tasks\ScoreBoardTask;
use Legacy\ThePit\utils\CurrencyUtils;
use Legacy\ThePit\utils\StatsUtils;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use Legacy\ThePit\scoreboard\ScoreBoardApi;
use Legacy\ThePit\scoreboard\module\ScoreBoard;

final class ScoreBoardsManager extends Managers
{
    public array $scoreboards = [];

    public function init(): void
    {
        ScoreBoardApi::loadManager();

        $scoreboards = Managers::DATA()->get("config")->get("scoreboards");
        foreach ($scoreboards as $type => $scoreboard) {
            if (!is_array($scoreboard)) continue;
            $this->scoreboards[$type] = [
                "id" => array_search($type, array_keys($scoreboards)) + 1,
                "scoreboard" => ($manager = ScoreBoardApi::getManager())?->getScoreBoard(
                    $manager?->createScoreBoard(array_search($type, array_keys($scoreboards)) + 1)
                )
            ];
            if (!isset($this->scoreboards[$type])) {
                Core::getInstance()->getLogger()->emergency("[SCOREBOARDS] Failed to load scoreboard: $type ... Retrying in 5 seconds");
                Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($type): void {
                    Core::getInstance()->getLogger()->notice("[SCOREBOARDS] Retrying to load scoreboard: $type");
                    $this->init();
                }), 5 * 20);
            } else Core::getInstance()->getLogger()->notice("[SCOREBOARDS] scoreboard: $type Loaded");
        }

        Core::getInstance()->getScheduler()->scheduleRepeatingTask(new ScoreBoardTask(), Managers::DATA()->get("config")->getNested("scoreboards.refresh-time", 20));
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->scoreboards;
    }

    public function get(string $name): ?ScoreBoard
    {
        return $this->scoreboards[$name];
    }

    public function getTitle(string $type): string
    {
        $type = $type === Event::NONE()->getName() ? "basic" : $type;
        return Managers::DATA()->get("config")->getNested("scoreboards.$type.title", "Legacy");
    }

    public function getLines(string $type): array
    {
        return Managers::DATA()->get("config")->getNested("scoreboards.$type.lines", []);
    }

    public function updateScoreboard(?ScoreBoard $scoreboard, string $type): void
    {
        foreach (Server::getInstance()->getOnlinePlayers() as $player) {
            foreach ($this->getAll() as $scoreboard) {
                $scoreboard["scoreboard"]->removePlayer($player);
            }
            $scoreboard = match (Managers::EVENTS()->getCurrentEvent()->getName()) {
                default => match ($player->getStatsProvider()->get(StatsUtils::PRESTIGE)) {
                    0 => $this->scoreboards["basic"]["scoreboard"],
                    default => $this->scoreboards["prestige"]["scoreboard"],
                },
                Event::DEATHMATCH()->getName() => $this->scoreboards[Event::DEATHMATCH()->getName()]["scoreboard"],
                Event::RAFFLE()->getName() => $this->scoreboards[Event::RAFFLE()->getName()]["scoreboard"],
                Event::SPIRE()->getName() => $this->scoreboards[Event::SPIRE()->getName()]["scoreboard"],
            };
            if ($player->getPlayerProperties()->getNestedProperties("parameters.scoreboard") ?? true) {
                $scoreboard?->addPlayer($player);
            }
            $scoreboard = $this->updateLines($scoreboard, $type, $player);
        }
        $scoreboard = $this->updateTitle($scoreboard, $type);
        $scoreboard?->sendToAll();
    }

    public function updateLines(ScoreBoard $scoreboard, string $type, Player|LegacyPlayer|null $player = null): ScoreBoard
    {
        if ($type === Event::NONE()->getName()) $type = ($player?->getStatsProvider()->get(StatsUtils::PRESTIGE)) >= 1 ? "prestige" : "basic";
        foreach (array_slice($this->getLines($type), 0, 15) as $i => $line) {
            foreach ($this->getParameters($type, $player) as $parameter => $value) {
                $line = str_replace($parameter, $value, $line);
            }
            $scoreboard->setLineToAll(new ScoreBoardLine($i + 1, $line));
        }
        return $scoreboard;
    }

    public function getParameters(string $type, ?LegacyPlayer $player): array
    {
        return match ($type) {
            "basic" => [
                "{level}" => $player?->getStatsProvider()->get(StatsUtils::LEVEL) ?: 1,
                "{xp}" => $player?->getStatsProvider()->get(StatsUtils::XP),
                "{or}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::GOLD),
                "{credits}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::CREDITS),
                "{etoiles}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::STARS),
                "{votecoins}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::VOTECOINS),
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "prestige" => [
                "{prestige}" => $player?->getStatsProvider()->get(StatsUtils::PRESTIGE),
                "{level}" => $player?->getStatsProvider()->get(StatsUtils::LEVEL) ?: 1,
                "{xp}" => $player?->getStatsProvider()->get(StatsUtils::XP) ?? 0,
                "{or}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::GOLD),
                "{credits}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::CREDITS),
                "{etoiles}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::STARS),
                "{votecoins}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::VOTECOINS),
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "deathmatch" => [
                "{timeleft}" => 0,
                "{kills}" => 0,
                "{position}" => 0,
                "{kills_blue}" => 0,
                "{kills_red}" => 0,
                "{prestige}" => $player?->getStatsProvider()->get(StatsUtils::PRESTIGE),
                "{level}" => $player?->getStatsProvider()->get(StatsUtils::LEVEL) ?: 1,
                "{xp}" => $player?->getStatsProvider()->get(StatsUtils::XP) ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "raffle" => [
                "{timeleft}" => 0,
                "{tickets}" => 0,
                "{position}" => 0,
                "{cagnotte}" => 0,
                "{prestige}" => $player?->getStatsProvider()->get(StatsUtils::PRESTIGE),
                "{level}" => $player?->getStatsProvider()->get(StatsUtils::LEVEL) ?: 1,
                "{xp}" => $player?->getStatsProvider()->get(StatsUtils::XP) ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "spire" => [
                "{timeleft}" => 0,
                "{stage}" => 0,
                "{kills}" => 0,
                "{prestige}" => $player?->getStatsProvider()->get(StatsUtils::PRESTIGE),
                "{level}" => $player?->getStatsProvider()->get(StatsUtils::LEVEL) ?: 1,
                "{xp}" => $player?->getStatsProvider()->get(StatsUtils::XP) ?? 0,
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            default => []

        };
    }

    public function updateTitle(?ScoreBoard $scoreboard, string $type): ?ScoreBoard
    {
        $scoreboard?->setDisplayName($this->getTitle($type));
        return $scoreboard;
    }
}