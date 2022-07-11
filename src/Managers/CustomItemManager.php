<?php

namespace Legacy\ThePit\Managers;

use Exception;
use Legacy\ThePit\Core;
use Legacy\ThePit\Items\CustomFood;
use Legacy\ThePit\Items\CustomSword;
use Legacy\ThePit\Items\List\Nemo;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\ItemIdentifier;
use pocketmine\item\StringToItemParser;
use pocketmine\item\ToolTier;
use pocketmine\item\VanillaItems;
use pocketmine\network\mcpe\convert\GlobalItemTypeDictionary;
use pocketmine\network\mcpe\convert\ItemTranslator;
use pocketmine\network\mcpe\protocol\ItemComponentPacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\serializer\ItemTypeDictionary;
use pocketmine\network\mcpe\protocol\types\CacheableNbt;
use pocketmine\network\mcpe\protocol\types\ItemComponentPacketEntry;
use pocketmine\network\mcpe\protocol\types\ItemTypeEntry;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\player\Player;
use pocketmine\scheduler\ClosureTask;
use pocketmine\world\Position;
use pocketmine\world\sound\ItemBreakSound;
use ReflectionClass;
use Webmozart\PathUtil\Path;
use const pocketmine\BEDROCK_DATA_PATH;

abstract class CustomItemManager
{
    const SCRIPTING = "scripting";
    const UPCOMING_CREATOR_FEATURES = "upcoming_creator_features";
    const GAMETEST = "gametest";
    const DATA_DRIVEN_ITEMS = "data_driven_items";
    const EXPERIMENTAL_MOLANG_FEATURES = "experimental_molang_features";

    /** @var Item[] */
    public static array $items = [];
    public static array $packetEntries = [];
    public static array $registered = [];
    public static array $handlers = [];
    public static ?ItemComponentPacket $packet = null;

    public static function initCustomItems(): void {

    }

    public static function registerItems()
    {
        CreativeInventory::getInstance()->clear();
        $ref = new ReflectionClass(ItemTranslator::class);
        $coreToNetMap = $ref->getProperty("simpleCoreToNetMapping");
        $netToCoreMap = $ref->getProperty("simpleNetToCoreMapping");
        $coreToNetMap->setAccessible(true);
        $netToCoreMap->setAccessible(true);
        $coreToNetValues = $coreToNetMap->getValue(ItemTranslator::getInstance());
        $netToCoreValues = $netToCoreMap->getValue(ItemTranslator::getInstance());
        $ref_1 = new ReflectionClass(ItemTypeDictionary::class);
        $itemTypeMap = $ref_1->getProperty("itemTypes");
        $itemTypeMap->setAccessible(true);
        $itemTypeEntries = $itemTypeMap->getValue(GlobalItemTypeDictionary::getInstance()->getDictionary());
        self::$packetEntries = [];
        foreach (self::getItemInCache() as $item) {
            $runtimeId = $item->getId() + ($item->getId() > 0 ? 5000 : -5000);
            $coreToNetValues[$item->getId()] = $runtimeId;
            $netToCoreValues[$runtimeId] = $item->getId();
            $itemTypeEntries[] = new ItemTypeEntry("custom:" . $item->getName(), $runtimeId, true);
            self::$packetEntries[] = new ItemComponentPacketEntry("custom:" . $item->getName(), new CacheableNbt($item->getComponents()));
            self::$registered[] = $item;
            $new = clone $item;
            StringToItemParser::getInstance()->register($item->getName() . ':custom', fn() => $new);
            ItemFactory::getInstance()->register($item, true);
            CreativeInventory::getInstance()->add($item);
            $netToCoreMap->setValue(ItemTranslator::getInstance(), $netToCoreValues);
            $coreToNetMap->setValue(ItemTranslator::getInstance(), $coreToNetValues);
            $itemTypeMap->setValue(GlobalItemTypeDictionary::getInstance()->getDictionary(), $itemTypeEntries);
            self::$packet = ItemComponentPacket::create(self::$packetEntries);
            Core::getInstance()->getLogger()->notice("[ITEMS] Custom Item: {$item->getName()} ({$item->getId()}) Loaded");
        }

        $creativeItems = json_decode(file_get_contents(Path::join(BEDROCK_DATA_PATH, "creativeitems.json")), true);
        foreach($creativeItems as $data){
            $item = Item::jsonDeserialize($data);
            if($item->getName() === "Unknown"){
                continue;
            }
            CreativeInventory::getInstance()->add($item);
        }
    }

    public static function register(Item ...$item){
        foreach ($item as $i) {
            try {
                self::$items[] = $i;
            } catch (Exception) {
                Core::getInstance()->getLogger()->error("[!] ". $item::class ." Is not custom item.");
            }
        }
    }

    public static function getItemInCache(): array {
        return self::$items;
    }

    public static function scheduleTask(Position $pos, Item $item, Player $player, float $breakTime, int $slot): void
    {

        $handler = Core::getInstance()->getScheduler()->scheduleDelayedTask(new ClosureTask(function () use ($pos, $item, $player, $breakTime, $slot): void {
            $pos->getWorld()->useBreakOn($pos, $item, $player);
            if ($item->getDamage() + 1 >= $item->getMaxDurability()) {
                $player->getInventory()->setItem($slot, VanillaItems::AIR());
                $player->getWorld()->addSound($player->getEyePos(), new ItemBreakSound());
            } else {
                $item->setDamage($item->getDamage() + 1);
                $player->getInventory()->setItem($slot, $item);
            }
            if ($breakTime > 0) {
                $player->getWorld()->broadcastPacketToViewers($pos, LevelEventPacket::create(LevelEvent::BLOCK_START_BREAK, (int)(65535 / $breakTime), $pos->asVector3()));
            }
            $item->applyDamage(1);
            unset(self::$handlers[$player->getName()][self::blockHash($pos)]);
        }), (int)floor($breakTime));
        if (!isset(self::$handlers[$player->getName()])) {
            self::$handlers[$player->getName()] = [];
        }
        self::$handlers[$player->getName()][self::blockHash($pos)] = $handler;
    }

    public static function blockHash(Position $pos): string
    {
        return implode(":", [$pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), $pos->getWorld()->getFolderName()]);
    }

    public static function stopTask(Player $player, Position $pos): void
    {
        if (!isset(self::$handlers[$player->getName()][self::blockHash($pos)])) {
            return;
        }
        $handler = self::$handlers[$player->getName()][self::blockHash($pos)];
        $handler->cancel();
        $player->getWorld()->broadcastPacketToViewers($pos, LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 1, $pos->asVector3()));
        unset(self::$handlers[$player->getName()][self::blockHash($pos)]);
    }

    public static function getPacket(): ?ItemComponentPacket {
        return self::$packet;
    }
}