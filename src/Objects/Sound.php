<?php

namespace Legacy\ThePit\Objects;

use Legacy\ThePit\Player\LegacyPlayer;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

final class Sound
{
    private PlaySoundPacket $packet;

    public function __construct(private string $name, int $volume = 1)
    {
        $this->packet = PlaySoundPacket::create($this->name, 0, 0, 0, $volume, 1);
    }

    public function play(LegacyPlayer ...$players): void
    {
        foreach ($players as $player) {
            $pk = clone $this->getPacket();
            $pk->x = $player->getLocation()->x;
            $pk->y = $player->getLocation()->y;
            $pk->z = $player->getLocation()->z;
            $player->getNetworkSession()->sendDataPacket($pk);
        }
    }

    public function getPacket(): PlaySoundPacket
    {
        return $this->packet;
    }

}