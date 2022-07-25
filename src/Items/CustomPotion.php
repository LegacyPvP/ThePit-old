<?php

declare(strict_types=1);

namespace Legacy\ThePit\items;

use pocketmine\entity\Living;
use pocketmine\item\Food;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\VanillaItems;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use Legacy\ThePit\Traits\CustomItemTrait;

class CustomPotion extends Food
{
    use CustomItemTrait;

    private bool $canAlwaysEat;
    private int $foodRestore;
    private string $textureName;
    private int $maxStackSize;
    private float $saturation;

    public function __construct(
        ItemIdentifier $identifier,
        string         $name,
        string         $textureName,
        bool           $canAlwaysEat,
        int            $foodRestore,
        float          $saturationRestore,
        int            $maxStackSize,

    )
    {
        $this->foodRestore = $foodRestore;
        $this->canAlwaysEat = $canAlwaysEat;
        $this->textureName = $textureName;
        $this->maxStackSize = $maxStackSize;
        $this->saturation = $saturationRestore;
        parent::__construct($identifier, $name);
    }

    public function onConsume(Living $consumer): void
    {
        if (!$consumer instanceof Player) return;
        if ($this->getCount() <= 1) {
            $consumer->getInventory()->setItemInHand(VanillaItems::AIR());
        } else $consumer->getInventory()->setItemInHand($this->setCount($this->getCount() - 1));

        $food = $consumer->getHungerManager()->getFood();
        $saturation = $consumer->getHungerManager()->getSaturation();

        if ($food + $this->getFoodRestore() >= 20) {
            $consumer->getHungerManager()->setFood(20);
        } else $consumer->getHungerManager()->setFood($food + $this->getFoodRestore());
        if ($saturation + $this->getSaturationRestore() >= 20.00) {
            $consumer->getHungerManager()->setSaturation(20.00);
        } else $consumer->getHungerManager()->setFood($saturation + $this->getSaturationRestore());
        parent::onConsume($consumer);
    }

    public function requiresHunger(): bool
    {
        return !$this->canAlwaysEat;
    }

    public function getComponents(): CompoundTag
    {
        return CompoundTag::create()
            ->setTag("components", CompoundTag::create()
                ->setTag("item_properties", CompoundTag::create()
                    ->setInt("max_stack_size", $this->getMaxStackSize())
                    ->setByte("allow_off_hand", 0)
                    ->setInt("use_duration", 32)
                    ->setInt("use_animation", 2)
                    ->setByte('can_destroy_in_creative', 1)
                    ->setInt("creative_category", 3)
                    ->setString("creative_group", "itemGroup.name.miscFood")
                    ->setTag("minecraft:icon", CompoundTag::create()
                        ->setString("texture", $this->getTextureName())
                        ->setString("legacy_id", 'custom:' . $this->getTextureName())
                    )
                )
                ->setTag('minecraft:food', CompoundTag::create()
                    ->setFloat('nutrition', floatval($this->getFoodRestore()))
                    ->setString('saturation_modifier', 'low')
                    ->setByte('can_always_eat', $this->canAlwaysEat() ? 1 : 0)
                )
                ->setShort("minecraft:identifier", $this->getRuntimeId($this->getId()))
                ->setTag("minecraft:display_name", CompoundTag::create()
                    ->setString("value", $this->checkName($this->getVanillaName()))
                )
            );
    }

    public function getTextureName(): string
    {
        return $this->textureName;
    }

    public function getFoodRestore(): int
    {
        return $this->foodRestore;
    }

    public function canAlwaysEat(): bool
    {
        return $this->canAlwaysEat;
    }

    public function getMaxStackSize(): int
    {
        return $this->maxStackSize;
    }

    public function getSaturationRestore(): float
    {
        return $this->saturation;
    }
}