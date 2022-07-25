<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\ArrayShape;
use Legacy\ThePit\Core;
use Legacy\ThePit\Objects\Rank;
use Legacy\ThePit\Player\LegacyPlayer;

final class RanksManager extends Managers
{
    /**
     * @var array
     */
    private array $ranks = [];

    public function init(): void
    {
        foreach (Managers::DATA()->get("config")->get('ranks', []) as $name => $grade) {
            $this->ranks[$name] = new Rank($name, $grade["permissions"], $grade["chat"], $grade["nametag"], $grade["scoretag"]);
            Core::getInstance()->getLogger()->notice("[RANKS] Rank: $name Loaded");
        }
    }

    /**
     * @return array
     */
    public function getAll(): array
    {
        return $this->ranks;
    }

    public function get(string $name): ?Rank
    {
        return $this->ranks[$name] ?? null;
    }

    public function getDefaultRank(): ?Rank
    {
        return reset($this->ranks) ?? null;
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