<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;

class NightvisionCommand extends Commands {

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if($this->testPermissionSilent($sender)){
            $sender_language = $this->getSenderLanguage($sender);
            if($sender instanceof LegacyPlayer){
                if($sender->isInNightvision() or $sender->getEffects()->has(VanillaEffects::NIGHT_VISION())){
                    $sender->setNightvision(false);
                    $sender->getEffects()->remove(VanillaEffects::NIGHT_VISION());
                    $sender_language->getMessage("messages.commands.nightvision.success-off")->send($sender);
                }else{
                    $sender->setNightvision(true);
                    $sender->getEffects()->add(new EffectInstance(VanillaEffects::NIGHT_VISION(), 999999999, 1, false));
                    $sender_language->getMessage("messages.commands.nightvision.success-on")->send($sender);
                }
            }
        }
    }
}
