<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Managers\RanksManager;
use pocketmine\command\CommandSender;

final class ListRankCommand extends Commands
{
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if($this->testPermissionSilent($sender)){
            $list = $this->getSenderLanguage($sender)->getMessage("messages.commands.listrank.header")->__toString();
            foreach(RanksManager::getRanks() as $rank){
                $list .= str_replace(["{name}"], [$rank->getName()], $this->getSenderLanguage($sender)->getMessage("messages.commands.listrank.format", [], false)->__toString());
            }
            $sender->sendMessage($list);
        }
    }
}