<?php

declare(strict_types=1);

namespace Legacy\ThePit\items;

use pocketmine\item\Armor;
use pocketmine\item\ArmorTypeInfo;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\ItemUseResult;
use pocketmine\math\Vector3;
use pocketmine\nbt\tag\CompoundTag;
use pocketmine\player\Player;
use pocketmine\Server;
use Legacy\ThePit\Traits\CustomItemTrait;

class CustomArmor extends Armor
{
    use CustomItemTrait;

    protected $lore = [''];

    const ARMOR_CLASS = [
        "gold",
        "none",
        "leather",
        "chain",
        "iron",
        "diamond",
        "elytra",
        "turtle",
        "netherite"
    ];


    const ARMOR_ENCHANT = [
        0 => 'armor_helmet',
        1 => 'armor_torso',
        2 => 'armor_legs',
        3 => 'armor_feet'
    ];

    const ARMOR_GROUP = [
        0 => 'itemGroup.name.helmet',
        1 => 'itemGroup.name.chestplate',
        2 => 'itemGroup.name.leggings',
        3 => 'itemGroup.name.boots'
    ];

    const ARMOR_WEARABLE = [
        0 => 'slot.armor.head',
        1 => 'slot.armor.chest',
        2 => 'slot.armor.legs',
        3 => 'slot.armor.feet'
    ];

    private string $textureName;
    private string $armorClass;

    public function __construct(
        ItemIdentifier $identifier,
        string         $name,
        ArmorTypeInfo  $info,
        string         $textureName,
        string         $classArmor = 'diamond'
    )
    {
        if (!in_array($classArmor, self::ARMOR_CLASS)) {
            Server::getInstance()->getLogger()->error("[CustomItemAPI] Error //: Item" . $this->getId() . ":" . $this->getMeta()) . ", armor class not found.";
        }
        $this->textureName = $textureName;
        $this->armorClass = $classArmor;
        parent::__construct($identifier, $name, $info);
    }

    public function onClickAir(Player $player, Vector3 $directionVector): ItemUseResult
    {
        $existing = $player->getArmorInventory()->getItem($this->getArmorSlot());
        $thisCopy = clone $this;
        $new = $thisCopy->pop();
        $player->getArmorInventory()->setItem($this->getArmorSlot(), $new);
        if ($thisCopy->getCount() === 0) {
            $player->getInventory()->setItemInHand($existing);
        } else { //if the stack size was bigger than 1 (usually won't happen, but might be caused by plugins
            $player->getInventory()->setItemInHand($thisCopy);
            $player->getInventory()->addItem($existing);
        }
        // TODO: add sound equip.
        return ItemUseResult::SUCCESS();
    }

    public function getComponents(): CompoundTag
    {
        return CompoundTag::create()
            ->setTag("components", CompoundTag::create()
                ->setTag("minecraft:durability", CompoundTag::create()
                    ->setShort("damage_change", 1)
                    ->setInt("max_durability", $this->getMaxDurability())
                )
                ->setTag("minecraft:armor", CompoundTag::create()
                    ->setString("texture_type", $this->getArmorClass())
                    ->setInt("protection", $this->getDefensePoints())
                )
                ->setTag("minecraft:wearable", CompoundTag::create()
                    ->setString("slot", self::ARMOR_WEARABLE[$this->getArmorSlot()]) // wtf mojang broke my plugin !
                    ->setByte("dispensable", 1)
                )
                ->setTag("item_properties", CompoundTag::create()
                    ->setInt("use_duration", 32)
                    ->setByte('can_destroy_in_creative', 0)
                    ->setInt("use_animation", 0)
                    ->setString("enchantable_slot", self::ARMOR_ENCHANT[$this->getArmorSlot()])
                    ->setInt("enchantable_value", 18)
                    ->setByte("creative_category", 3)
                    ->setInt("max_stack_size", 1)
                    ->setInt("creative_category", 3)
                    ->setString("creative_group", self::ARMOR_GROUP[$this->getArmorSlot()])
                    ->setTag("minecraft:icon", CompoundTag::create()
                        ->setString("texture", $this->getTextureName())
                        ->setString("legacy_id", "custom:" . $this->name)
                    )
                )
            )
            ->setShort("minecraft:identifier", $this->getId() + ($this->getId() > 0 ? 5000 : -5000))
            ->setTag("minecraft:display_name", CompoundTag::create()
                ->setString("value", $this->checkName($this->getVanillaName()))
            )
            ->setTag("minecraft:on_use", CompoundTag::create()
                ->setByte("on_use", 1)
            )->setTag("minecraft:on_use_on", CompoundTag::create()
                ->setByte("on_use_on", 1)
            );
    }


    public function getTextureName(): string
    {
        return $this->textureName;
    }

    public function getArmorClass(): string
    {
        return $this->armorClass;
    }
}