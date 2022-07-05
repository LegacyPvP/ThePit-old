<?php

namespace legacy\world\manager;

use JetBrains\PhpStorm\Pure;
use JsonException;
use legacy\world\Main;
use legacy\world\templates\Area;
use pocketmine\utils\Config;
use pocketmine\world\Position;

final class AreaManager
{
    public array $cache;
    private Config $microDatabase;

    public function __construct(private Main $plugin)
    {
        $path = $plugin->getDataFolder();
        $this->microDatabase = new Config($path . '/temp/db.json', Config::JSON);
        $this->cache = $this->microDatabase->getAll();
    }


    /**
     * @throws JsonException
     */
    public function saveAllData(): void
    {
        $this->microDatabase->setAll($this->cache);
        $this->microDatabase->save();
    }

    /**
     * @param string $name
     * @return array
     */
    #[Pure] public function getFlagsByName(string $name): array
    {
        if (isset($this->cache[$name])) {
            return $this->cache[$name]['flags'];
        }
        return Area::createBaseFlags();
    }

    /**
     * @param string $name
     * @param array $flags
     */
    public function setFlagsByName(string $name, array $flags): void
    {
        if (isset($this->cache[$name])) {
            $this->cache[$name]['flags'] = $flags;
        }
    }

    /**
     * @param string $name
     * @param Position $pos1
     * @param Position $pos2
     */
    public function setPositionByName(string $name, Position $pos1, Position $pos2): void
    {
        if (isset($this->cache[$name])) {
            $minimumX = intval(min($pos1->getX(), $pos2->getX()));
            $maximumX = intval(max($pos1->getX(), $pos2->getX()));
            $minimumZ = intval(min($pos1->getZ(), $pos2->getZ()));
            $maximumZ = intval(max($pos1->getZ(), $pos2->getZ()));
            $string = $minimumX . ':' . $maximumX . ':' . $minimumZ . ':' . $maximumZ;
            $this->cache[$name]['positions'] = $string;
        }
    }

    public function deleteAreaByName(string $name): void
    {
        if (isset($this->cache[$name])) {
            unset($this->cache[$name]);
        }
    }

    /**
     * @param Area $area
     */
    public function createArea(Area $area): void
    {
        $name = $area->getName();
        $positions = $area->getStringPosition();
        $flags = $area->getFlags();
        $this->cache[$name] = ['positions' => $positions, 'flags' => $flags, 'priority' => $area->getPriority()];
    }


    public function isInArea(Position $position): bool
    {
        $x = $position->getX();
        $z = $position->getZ();
        foreach ($this->cache as $value) {
            $stringExplode = explode(':', $value['positions']);
            if ($x >= $stringExplode[0] && $x <= $stringExplode[1]) {
                if ($z >= $stringExplode[2] && $z <= $stringExplode[3]) {
                    if ($stringExplode[4] === $position->getWorld()->getFolderName()) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    public function getPriorityByAreaName(string $name): int {
        return $this->cache[$name]['priority'];
    }

    public function getFlagsAreaByPosition(Position $position): array
    {
        $x = $position->getX();
        $z = $position->getZ();


        $areaName = null;
        $priority = -1;

        foreach ($this->cache as $name => $value) {
            $stringExplode = explode(':', $value['positions']);
            if ($x >= $stringExplode[0] && $x <= $stringExplode[1]) {
                if ($z >= $stringExplode[2] && $z <= $stringExplode[3]) {
                    if ($stringExplode[4] === $position->getWorld()->getFolderName()) {
                        $prio = $this->getPriorityByAreaName($name);
                        if ($prio > $priority) {
                            $priority = $prio;
                            $areaName = $name;
                        }
                    }
                }
            }
        }

        if ($areaName === null) {
            return [
                'pvp' => true,
                'break' => true,
                'place' => true,
                'hunger' => true,
                'dropItem' => true,
                'chat' => true,
                'cmd' => true,
                'tnt' => true
            ];
        } else {
            return $this->getFlagsByName($areaName);
        }
    }

    public function getNameAreaByPosition(Position $position): string
    {
        $x = $position->getX();
        $z = $position->getZ();
        foreach ($this->cache as $name => $value) {
            $stringExplode = explode(':', $value['positions']);
            if ($x >= $stringExplode[0] && $x <= $stringExplode[1]) {
                if ($z >= $stringExplode[2] && $z <= $stringExplode[3]) {
                    if ($stringExplode[4] === $position->getWorld()->getFolderName()) {
                        return $name;
                    }
                }
            }
        }
        return '404';
    }

    /**
     * @return Main
     */
    public function getPlugin(): Main
    {
        return $this->plugin;
    }
}