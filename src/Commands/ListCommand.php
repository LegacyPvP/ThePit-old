<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Managers\RanksManager;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;

class ListCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            if($sender instanceof LegacyPlayer){
                if(!isset($args[0])){
                    $sender->getLanguage()->getMessage("messages.commands.list.header")->send($sender);
                    $players = array_map(function(LegacyPlayer $player) : string {
                        return str_replace("{player}", $player->getName(), $player->getRank()->getNametag($player));
                    }, array_filter($sender->getServer()->getOnlinePlayers(), function(LegacyPlayer $player) use ($sender) : bool {
                        return $player->isOnline() or $player->getName() !== $sender->getName();
                    }));
                    sort($players, SORT_STRING);
                    $sender->sendMessage(implode(", ", $players));
                }else{
                    if($args[0] == "staff" or "staffs"){
                        $sender->getLanguage()->getMessage("messages.commands.list.staff-header")->send($sender);
                        $players = array_map(function(LegacyPlayer $player): string {
                                return str_replace("{player}", $player->getName(), $player->getRank()->getNametag($player));
                        }, array_filter($sender->getServer()->getOnlinePlayers(), function(LegacyPlayer $player) use ($sender) : bool {
                            $staff_ranks = [
                                "Admin",
                                "Développeur",
                                "Responsable",
                                "Super-Modérateur",
                                "Modérateur",
                                "Guide"
                            ];
                            return $player->isOnline() and in_array($player->getRank()->getName(), $staff_ranks);
                        }));
                        sort($players, SORT_STRING);
                        $sender->sendMessage(implode(", ", $players));
                    }
                }
            }
        }
    }
}
