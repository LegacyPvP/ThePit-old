<?php

namespace Legacy\ThePit\providers;

use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\quest\Quest;

final class QuestProvider
{
    public function __construct(private LegacyPlayer $player)
    {
    }

    /**
     * @return Quest[]
     */
    public function getAll(): array
    {
        return $this->player->getPlayerProperties()->getProperties("quests");
    }

    public function get(string $quest): ?Quest
    {
        return $this->player->getPlayerProperties()->getNestedProperties("quests.$quest") ?? null;
    }

    public function add(Quest $quest): void
    {
        $this->player->getPlayerProperties()->setProperties("quests", $this->getAll()[$quest->getName()] = $quest);
    }

    public function remove($quest): void
    {
        if (!isset($this->getAll()[$quest])) return;
        $name = $quest;
        if ($name instanceof Quest) $name = $name->getName();
        $new = $this->getAll()[$name];
        unset($this->getAll()[$name]);
        $this->player->getPlayerProperties()->setProperties("quests", $new);
    }

    public function has($quest): bool
    {
        $name = $quest;
        if ($name instanceof Quest) $name = $name->getName();
        return isset($this->getAll()[$name]);
    }
}