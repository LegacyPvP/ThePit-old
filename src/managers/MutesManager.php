<?php

namespace Legacy\ThePit\managers;

use Legacy\ThePit\player\LegacyPlayer;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\nbt\tag\IntTag;
use pocketmine\nbt\tag\StringTag;
use pocketmine\player\IPlayer;
use pocketmine\player\OfflinePlayer;
use pocketmine\player\Player;
use pocketmine\Server;

final class MutesManager extends Managers
{
    private const DEFAULT_REASON = "Aucune raison donnée";
    private const DEFAULT_STAFF = "CONSOLE";

    public function isMuted(LegacyPlayer $player): bool
    {
        return $this->getTime($player) > time();
    }

    public function setMuted(LegacyPlayer $player, int $time, string $reason = self::DEFAULT_REASON, string $staff = self::DEFAULT_STAFF): void
    {
        $player->getPlayerProperties()->setNestedProperties("mute.time", $time);
        $player->getPlayerProperties()->setNestedProperties("mute.reason", $reason);
        $player->getPlayerProperties()->setNestedProperties("mute.staff", $staff);
    }

    public function getTime(LegacyPlayer $player): int
    {
        return $player->getPlayerProperties()->getNestedProperties("mute.time");
    }

    public function getReason(LegacyPlayer $player): string
    {
        return $player->getPlayerProperties()->getNestedProperties("mute.reason");
    }

    public function getStaff(LegacyPlayer $player): string
    {
        return $player->getPlayerProperties()->getNestedProperties("mute.staff");
    }

    public static function removeMute(IPlayer $player)
    {
        switch ($player::class) {
            case Player::class:
            case LegacyPlayer::class:
                $player->getPlayerProperties()->setNestedProperties("mute.time", 0);
                $player->getPlayerProperties()->setNestedProperties("mute.reason", self::DEFAULT_REASON);
                $player->getPlayerProperties()->setNestedProperties("mute.staff", self::DEFAULT_STAFF);
                break;
            case OfflinePlayer::class:
            default:
                $properties = Server::getInstance()->getOfflinePlayerData($player->getName())->getCompoundTag('properties') ?? new CompoundTag();
                $nbt = (new CompoundTag())->merge($properties);
                $nbt->setTag("mute",
                    (new CompoundTag())
                        ->setTag("time", new IntTag(0))
                        ->setTag("reason", new StringTag(self::DEFAULT_REASON))
                        ->setTag("staff", new StringTag(self::DEFAULT_STAFF)));
                $properties->setTag("properties", $nbt);
                Server::getInstance()->saveOfflinePlayerData($player->getName(), $properties);

        }
    }

}