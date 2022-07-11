<?php

declare(strict_types=1);

namespace Legacy\ThePit\Items;

use pocketmine\item\Item;
use pocketmine\item\ItemIdentifier;
use pocketmine\nbt\tag\CompoundTag;
use Legacy\ThePit\Traits\CustomItemTrait;

class BaseItem extends Item
{
    use CustomItemTrait;

    private string $textureName;
    private int $maxStackSize;
    private bool $allowOffHand;
    private bool $handEquipped = false;

    public function __construct(
        ItemIdentifier $identifier,
        string $name,
        string $textureName,
        int $maxStackSize,
        bool $allowOffHand,
    )
    {
        $this->textureName = $textureName;
        $this->maxStackSize = $maxStackSize;
        $this->allowOffHand = $allowOffHand;
        parent::__construct($identifier, $name);
    }

    public function getComponents(): CompoundTag {
        return CompoundTag::create()->setTag("components", CompoundTag::create()
            ->setTag("item_properties", CompoundTag::create()
                ->setInt("use_duration", 32)
                ->setInt("use_animation", 0)
                ->setByte("allow_off_hand", $this->allowOffHand() ? 1 : 0)
                ->setByte("can_destroy_in_creative", 0)
                ->setInt("creative_category", 3)
                ->setByte("hand_equipped", $this->getHandEquipped() ? 1 : 0)
                ->setInt("max_stack_size", $this->getMaxStackSize())
                ->setFloat("mining_speed", 1)
                ->setTag("minecraft:icon", CompoundTag::create()
                    ->setString("texture", $this->getTextureName())
                    ->setString("legacy_id", 'custom:' . $this->getTextureName())
                )
            )
        )
            ->setShort("minecraft:identifier", $this->getRuntimeId($this->getId()))
            ->setTag("minecraft:display_name", CompoundTag::create()
                ->setString("value", $this->checkName($this->getVanillaName()))
            )
            ->setTag("minecraft:on_use", CompoundTag::create()
                ->setByte("on_use", 1)
            )->setTag("minecraft:on_use_on", CompoundTag::create()
                ->setByte("on_use_on", 1)
            );
    }

    public function getMaxStackSize(): int {
        return $this->maxStackSize;
    }

    public function getTextureName(): string {
        return $this->textureName;
    }

    public function getHandEquipped(): bool {
        return $this->handEquipped;
    }

    public function allowOffHand(): bool {
        return $this->allowOffHand;
    }
}