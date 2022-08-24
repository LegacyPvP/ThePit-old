<?php

namespace Legacy\ThePit\listeners;

use Legacy\ThePit\forms\element\Button;
use Legacy\ThePit\forms\variant\SimpleForm;
use Legacy\ThePit\managers\Managers;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\tiles\list\CrateTile;
use pocketmine\block\Chest;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent as ClassEvent;
use pocketmine\item\StringToItemParser;
use pocketmine\player\Player;
use pocketmine\Server;

final class PlayerInteractEvent implements Listener
{
    final public function onEvent(ClassEvent $event)
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        if (!$player instanceof LegacyPlayer) return;
        if ($block instanceof Chest) {
            if ($player->getInventory()->getItemInHand()->getId() == StringToItemParser::getInstance()->parse("rabbit_foot")) {
                if (Server::getInstance()->isOp($player->getName())) {

                    $form = new SimpleForm("Crate", "Choose a create");

                    foreach (Managers::CRATES()->getAll() as $crate) {
                        $form->addButton(new Button($crate->getName(), null, function (Player $player) use ($crate, $block) {
                            $crate->createCrate($block);
                        }));
                    }
                    $form->addButton(new Button("§cCancel"));
                    $player->sendForm($form);
                }
            } else {
                $tile = $block->getPosition()->getWorld()->getTile($block->getPosition());
                if (!$tile instanceof CrateTile) return;
                if ($player->getPlayerProperties()->getNestedProperties("keys." . $tile->getName()) > 0) {
                    $player->getPlayerProperties()->setNestedProperties("keys." . $tile->getName(), (int)$player->getPlayerProperties()->getNestedProperties("keys." . $tile->getName()) - 1);
                    $tile->giveReward($player);
                }
            }
        }
    }
}