<?php

namespace Legacy\ThePit\perks;

use Legacy\ThePit\traits\PerkTrait;
use pocketmine\utils\EnumTrait;

/**
 * This doc-block is generated automatically, do not modify it manually.
 * This must be regenerated whenever registry members are added, removed or changed.
 * @see build/generate-registry-annotations.php
 * @generate-registry-docblock
 *
 * @method static BountyHunter BOUNTYHUNTER()
 * @method static Flash FLASH()
 * @method static GoldenHead GOLDENHEAD()
 * @method static Nabbit NABBIT()
 * @method static SerialKiller SERIALKILLER()
 */
abstract class Perk
{
//     use PerkTrait;

    use EnumTrait {
        __construct as Enum___construct;
    }

    public static function setup(): void
    {
        self::registerAll(
            new BountyHunter("bountyhunter", "", 0),
            new Flash("flash", "", 0),
            new GoldenHead("goldenhead", "", 0),
            new Nabbit("nabbit", "", 0),
            new SerialKiller("serialkiller", "", 0),
        );
    }

    protected function __construct(private string $name = "base", private string $description = "", private int $price = 0)
    {
        $this->Enum___construct($name);
    }

    final public function getName(): string
    {
        return $this->name;
    }

    final public function getDescription(): string
    {
        return $this->description;
    }

    final public function getPrice(): string
    {
        return $this->price;
    }

    abstract public function onEvent(): string;
}