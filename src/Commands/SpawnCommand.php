<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;

class SpawnCommand extends Commands
{

    private int $time = 4;
    private Location $location;

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            if ($sender instanceof LegacyPlayer) {
                $sender_language = $this->getSenderLanguage($sender);
                if ($sender->isInTeleportation()) {
                    $sender->sendMessage($this->getSenderLanguage($sender)->getMessage("messages.commands.spawn.already-in-teleportation"));
                } else {
                    $sender->setTeleportation(true);
                    $this->location = $sender->getLocation();
                    $sender->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 10, 1));
                    Core::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($sender, $sender_language) {
                        try {
                            switch ($this->time) {
                                case 5:
                                case 4:
                                case 3:
                                case 2:
                                    $this->time--;
                                    $x = $this->location->getX();
                                    $y = $this->location->getY();
                                    $z = $this->location->getZ();
                                    $playerX = $sender->getLocation()->getX();
                                    $playerY = $sender->getLocation()->getY();
                                    $playerZ = $sender->getLocation()->getZ();
                                    if ($sender->isOnline()) {
                                        if ($x != $playerX or $y != $playerY or $z != $playerZ) {
                                            $sender_language->getMessage("messages.commands.spawn.teleportation-cancelled")->sendPopup($sender);
                                            $sender->setTeleportation(false);
                                            $this->time = 4;
                                            $sender->getEffects()->remove(VanillaEffects::BLINDNESS());
                                            throw new CancelTaskException();
                                        } else {
                                            $sender_language->getMessage("messages.commands.spawn.teleportation-in-progress", [
                                                "{time}" => $this->time
                                            ])->sendPopup($sender);
                                        }
                                    } else {
                                        $sender_language->getMessage("messages.commands.spawn.teleportation-cancelled")->sendPopup($sender);
                                        $sender->setTeleportation(false);
                                        $this->time = 4;
                                        $sender->getEffects()->remove(VanillaEffects::BLINDNESS());
                                        throw new CancelTaskException();
                                    }
                                case 1:
                                    $this->time = 4;
                                    $sender_language->getMessage("messages.commands.spawn.success", [
                                        "{time}" => $this->time
                                    ])->sendPopup($sender);
                                    $sender->getEffects()->remove(VanillaEffects::BLINDNESS());
                                    $sender->teleport(Core::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
                                    $sender->setTeleportation(false);
                                    throw new CancelTaskException();
                            }
                        } catch (LanguageException $exception) {
                            $this->getSenderLanguage($sender)->getMessage($exception->getMessage(), $exception->getArgs(), $exception->getPrefix())->send($sender);
                        }
                    }), 20);
                }
            }
        }
    }
}
