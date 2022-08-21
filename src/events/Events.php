<?php

namespace Legacy\ThePit\events;

use Legacy\ThePit\Core;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\EnumTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static Events NONE()
 *
 * TODO: MINOR
 *
 * @method static DoubleReward DOUBLEREWARD()
 * @method static Bounty BOUNTY()
 *
 * TODO: MAJOR
 *
 * @method static DeathMatch DEATHMATCH()
 * @method static Raffle RAFFLE()
 * @method static Spire SPIRE()
 */
abstract class Events
{
    use EnumTrait {
        __construct as Enum___construct;
    }

    protected const TYPE_MINOR = 0 >> 2;
    protected const TYPE_MAJOR = 5 >> 2;

    public static function setup(): void
    {
        self::registerAll(
            new None("none", "Do nothing", 0),

            // MINOR
            new DoubleReward("doublereward", "", 300),
            new Bounty("bounty", "", 300),

            // MAJOR
            new DeathMatch("deathmatch", "", 300),
            new Raffle("raffle", "", 300),
            new Spire("spire", "", 300)
        );
    }

    private function __construct(protected string $name, protected string $description, protected int $duration)
    {
        $this->enumName = $name;
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function getDescription(): string
    {
        return $this->description;
    }

    final public function getDuration(): string
    {
        return $this->duration;
    }

    abstract public function stop(): void;

    public function getType(): int
    {
        return -1;
    }

    public function start(): void
    {
        Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(fn() => $this->stop()), $this->duration * 20);
    }

    public function __toString(): string
    {
        return $this->getName();
    }
}