<?php

declare(strict_types=1);

namespace Legacy\ThePit\Items;

use pocketmine\item\ItemIdentifier;
use pocketmine\item\Pickaxe;
use pocketmine\item\ToolTier;
use pocketmine\nbt\tag\CompoundTag;
use Legacy\ThePit\Traits\CustomItemTrait;

class CustomPickaxe extends Pickaxe
{

    use CustomItemTrait;

    private string $textureName;
    private float $miningSpeed;
    private int $durability;
    private int $attackPoints;


    public function __construct(
        ItemIdentifier $identifier,
        string         $name,
        ToolTier       $tier,
        string         $textureName,
        float          $miningSpeed,
        int            $durability,
        int            $attackPoints
    )
    {
        $this->textureName = $textureName;
        $this->miningSpeed = $miningSpeed;
        $this->durability = $durability;
        $this->attackPoints = $attackPoints;
        parent::__construct($identifier, $name, $tier);
    }

    public function getComponents(): CompoundTag
    {
        return CompoundTag::create()
            ->setTag("components", CompoundTag::create()
                ->setTag("item_properties", CompoundTag::create()
                    ->setInt("max_stack_size", 1)
                    ->setByte("hand_equipped", 1)
                    ->setInt("damage", $this->attackPoints)
                    ->setInt("creative_category", 3)
                    ->setString("creative_group", "itemGroup.name.pickaxe")
                    ->setString("enchantable_slot", "pickaxe")
                    ->setInt("enchantable_value", 10)
                    ->setByte('can_destroy_in_creative', 1)
                    ->setTag("minecraft:icon", CompoundTag::create()
                        ->setString("texture", $this->getTextureName())
                    )
                )
                ->setTag("minecraft:weapon", CompoundTag::create()
                    ->setTag("on_hurt_entity", CompoundTag::create()
                        ->setString("event", "event")
                    )
                )
                ->setTag("minecraft:durability", CompoundTag::create()
                    ->setInt("max_durability", $this->getMaxDurability())
                )
                ->setShort("minecraft:identifier", $this->getRuntimeId($this->getId()))
                ->setTag("minecraft:display_name", CompoundTag::create()
                    ->setString("value", 'item.' . str_replace(' ', '_', strtolower($this->getName())) . '.name')
                )
            );
    }


    public function getTextureName(): string
    {
        return $this->textureName;
    }

    public function getMaxDurability(): int
    {
        return $this->durability;
    }

    public function getAttackPoints(): int
    {
        return $this->attackPoints;
    }

    public function getBaseMiningEfficiency(): float
    {
        return $this->miningSpeed;
    }
}