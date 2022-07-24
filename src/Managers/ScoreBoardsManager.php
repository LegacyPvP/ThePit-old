<?php

namespace Legacy\ThePit\Managers;

use Legacy\ThePit\Core;
use Legacy\ThePit\ScoreBoard\module\types\ScoreBoardLine;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Tasks\ScoreBoardTask;
use Legacy\ThePit\Utils\CurrencyUtils;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\Server;
use Legacy\ThePit\ScoreBoard\ScoreBoardApi;
use Legacy\ThePit\ScoreBoard\module\ScoreBoard;

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
                Core::getInstance()->getLogger()->emergency("[SCOREBOARDS] Failed to load ScoreBoard: $type ... Retrying in 5 seconds");
                Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($type): void {
                    Core::getInstance()->getLogger()->notice("[SCOREBOARDS] Retrying to load ScoreBoard: $type");
                    $this->init();
                }), 5 * 20);
            } else Core::getInstance()->getLogger()->notice("[SCOREBOARDS] ScoreBoard: $type Loaded");
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
        $type = $type === Managers::EVENTS()::TYPE_NONE ? "basic" : $type;
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
            $scoreboard = match ($type) {
                Managers::EVENTS()::TYPE_NONE => match ($player->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0) {
                    0 => $this->scoreboards["basic"]["scoreboard"],
                    default => $this->scoreboards["prestige"]["scoreboard"],
                },
                Managers::EVENTS()::TYPE_DEATHMATCH => $this->scoreboards[Managers::EVENTS()::TYPE_DEATHMATCH]["scoreboard"],
                Managers::EVENTS()::TYPE_RAFFLE => $this->scoreboards[Managers::EVENTS()::TYPE_RAFFLE]["scoreboard"],
                Managers::EVENTS()::TYPE_SPIRE => $this->scoreboards[Managers::EVENTS()::TYPE_SPIRE]["scoreboard"],
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
        if ($type === Managers::EVENTS()::TYPE_NONE) $type = ($player?->getPlayerProperties()?->getNestedProperties("stats.prestige") ?? 0) >= 1 ? "prestige" : "basic";
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
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{or}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::GOLD),
                "{credits}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::CREDITS),
                "{etoiles}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::STARS),
                "{votecoins}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::VOTECOINS),
                "{online}" => count(Server::getInstance()->getOnlinePlayers())
            ],
            "prestige" => [
                "{prestige}" => $player?->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0,
                "{level}" => $player?->getPlayerProperties()->getNestedProperties("stats.level") ?? 1,
                "{xp}" => $player?->getPlayerProperties()->getNestedProperties("stats.xp") ?? 0,
                "{or}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::GOLD),
                "{credits}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::CREDITS),
                "{etoiles}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::STARS),
                "{votecoins}" => $player?->getCurrencyProvider()?->get(CurrencyUtils::VOTECOINS),
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

    public function updateTitle(?ScoreBoard $scoreboard, string $type): ?ScoreBoard
    {
        $scoreboard?->setDisplayName($this->getTitle($type));
        return $scoreboard;
    }
}