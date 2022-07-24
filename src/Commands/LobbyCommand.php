<?php

namespace Legacy\ThePit\Commands;

use Legacy\ThePit\Core;
use Legacy\ThePit\Exceptions\LanguageException;
use Legacy\ThePit\Managers\Managers;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\command\CommandSender;
use pocketmine\entity\effect\EffectInstance;
use pocketmine\entity\effect\VanillaEffects;
use pocketmine\entity\Location;
use pocketmine\scheduler\CancelTaskException;
use pocketmine\scheduler\ClosureTask;

final class LobbyCommand extends Commands
{

    private int $time = 4;
    private Location $location;

    public function execute(CommandSender $sender, string $commandLabel, array $args): void
    {
        if ($this->testPermissionSilent($sender)) {
            if ($sender instanceof LegacyPlayer) {
                $sender_language = $this->getSenderLanguage($sender);
                if ($sender->isInTeleportation()) {
                    $sender_language->getMessage("messages.commands.lobby.already-in-teleportation")->send($sender);
                } else {
                    $sender->setTeleportation(true);
                    $this->location = $sender->getLocation();
                    $sender->getEffects()->add(new EffectInstance(VanillaEffects::BLINDNESS(), 20 * 10, 1));
                    Core::getInstance()->getScheduler()->scheduleRepeatingTask(new ClosureTask(function () use ($sender, $sender_language) {
                        try {
                            switch ($this->time) {
                                case 4:
                                case 3:
                                case 2:
                                case 1:
                                    $this->time--;
                                    $x = $this->location->getX();
                                    $y = $this->location->getY();
                                    $z = $this->location->getZ();
                                    $playerX = $sender->getLocation()->getX();
                                    $playerY = $sender->getLocation()->getY();
                                    $playerZ = $sender->getLocation()->getZ();
                                    if ($sender->isOnline()) {
                                        if ($x != $playerX or $y != $playerY or $z != $playerZ) {
                                            $sender_language->getMessage("messages.commands.lobby.teleportation-cancelled")->sendPopup($sender);
                                            $sender->setTeleportation(false);
                                            $this->time = 4;
                                            $sender->getEffects()->remove(VanillaEffects::BLINDNESS());
                                            throw new CancelTaskException();
                                        } else {
                                            throw new LanguageException("messages.commands.lobby.teleportation-in-progress", [
                                                "{time}" => $this->time
                                            ], ServerUtils::PREFIX_2);
                                        }
                                    } else {
                                        $sender_language->getMessage("messages.commands.lobby.teleportation-cancelled")->sendPopup($sender);
                                        $sender->setTeleportation(false);
                                        $this->time = 4;
                                        $sender->getEffects()->remove(VanillaEffects::BLINDNESS());
                                        throw new CancelTaskException();
                                    }
                                case 0:
                                    $this->time = 4;
                                    $sender_language->getMessage("messages.commands.lobby.success", [
                                        "{time}" => $this->time
                                    ])->sendPopup($sender);
                                    $sender->teleport(Core::getInstance()->getServer()->getWorldManager()->getDefaultWorld()->getSpawnLocation());
                                    $sender->setTeleportation(false);
                                    $sender->getEffects()->remove(VanillaEffects::BLINDNESS());
                                    $sender->transfer(Managers::DATA()->get("config")->getNested("server.lobby.ip"), Managers::DATA()->get("config")->getNested("server.lobby.port"));
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