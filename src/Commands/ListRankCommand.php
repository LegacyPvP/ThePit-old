<?php

namespace Legacy\ThePit\commands;

use Legacy\ThePit\managers\Managers;
use pocketmine\command\CommandSender;

final class ListRankCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            $list = $this->getSenderLanguage($sender)->getMessage("messages.commands.listrank.header")->__toString();
            foreach (Managers::RANKS()->getAll() as $rank) {
                $list .= str_replace(["{name}"], [$rank->getName()], $this->getSenderLanguage($sender)->getMessage("messages.commands.listrank.format", [], false)->__toString());
            }
            $sender->sendMessage($list);
        }
    }
}