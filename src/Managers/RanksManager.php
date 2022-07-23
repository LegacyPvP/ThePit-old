<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\ArrayShape;
use Legacy\ThePit\Core;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Player\LegacyPlayer;

abstract class RanksManager
{
    /**
     * @var array
     */
    private static array $ranks = [];

    public static function initRanks(): void
    {
        foreach (Core::getInstance()->getConfig()->get('ranks', []) as $name => $grade) {
            self::$ranks[$name] = new Rank($name, $grade["permissions"], $grade["chat"], $grade["nametag"], $grade["scoretag"]);
            Core::getInstance()->getLogger()->notice("[RANKS] Rank: $name Loaded");
        }
    }

    /**
     * @return array
     */
    public static function getRanks(): array
    {
        return self::$ranks;
    }

    public static function parseRank(string $grade): ?Rank
    {
        return self::$ranks[$grade] ?? null;
    }

    public static function getDefaultRank(): ?Rank
    {
        return reset(self::$ranks) ?? null;
    }

    #[ArrayShape(["{player}" => "string", "{prestige}" => "int", "{niveau}" => "int"])]
    public static function getParameters(LegacyPlayer $player): array
    {
        return [
            "{player}" => $player->getName(),
            "{prestige}" => $player->getPlayerProperties()->getNestedProperties("stats.prestige") ?? 0,
            "{niveau}" => $player->getPlayerProperties()->getNestedProperties("stats.niveau") ?? 1,
        ];
    }

}