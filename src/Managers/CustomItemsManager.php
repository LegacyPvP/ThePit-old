<?php

namespace Legacy\ThePit\managers;

use Exception;
use Legacy\ThePit\Core;
use pocketmine\inventory\CreativeInventory;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\item\StringToItemParser;
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

final class CustomItemsManager extends Managers
{
    const SCRIPTING = "scripting";
    const UPCOMING_CREATOR_FEATURES = "upcoming_creator_features";
    const GAMETEST = "gametest";
    const DATA_DRIVEN_ITEMS = "data_driven_items";
    const EXPERIMENTAL_MOLANG_FEATURES = "experimental_molang_features";

    /** @var Item[] */
    public array $items = [];
    public array $packetEntries = [];
    public array $registered = [];
    public array $handlers = [];
    public ?ItemComponentPacket $packet = null;

    public function load(): void
    {

    }

    public function init(): void
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
        $this->packetEntries = [];
        foreach ($this->getAll() as $item) {
            $runtimeId = $item->getId() + ($item->getId() > 0 ? 5000 : -5000);
            $coreToNetValues[$item->getId()] = $runtimeId;
            $netToCoreValues[$runtimeId] = $item->getId();
            $itemTypeEntries[] = new ItemTypeEntry("custom:" . $item->getName(), $runtimeId, true);
            $this->packetEntries[] = new ItemComponentPacketEntry("custom:" . $item->getName(), new CacheableNbt($item->getComponents()));
            $this->registered[] = $item;
            $new = clone $item;
            StringToItemParser::getInstance()->register($item->getName() . ':custom', fn() => $new);
            ItemFactory::getInstance()->register($item, true);
            CreativeInventory::getInstance()->add($item);
            $netToCoreMap->setValue(ItemTranslator::getInstance(), $netToCoreValues);
            $coreToNetMap->setValue(ItemTranslator::getInstance(), $coreToNetValues);
            $itemTypeMap->setValue(GlobalItemTypeDictionary::getInstance()->getDictionary(), $itemTypeEntries);
            $this->packet = ItemComponentPacket::create($this->packetEntries);
            Core::getInstance()->getLogger()->notice("[ITEMS] Custom Item: {$item->getName()} ({$item->getId()}) Loaded");
        }

        $creativeItems = json_decode(file_get_contents(Path::join(BEDROCK_DATA_PATH, "creativeitems.json")), true);
        foreach ($creativeItems as $data) {
            $item = Item::jsonDeserialize($data);
            if ($item->getName() === "Unknown") {
                continue;
            }
            CreativeInventory::getInstance()->add($item);
        }
    }

    public function registerItems(Item ...$item)
    {
        foreach ($item as $i) {
            try {
                $this->items[] = $i;
            } catch (Exception) {
                Core::getInstance()->getLogger()->error("[!] " . $item::class . " Is not custom item.");
            }
        }
    }

    public function getAll(): array
    {
        return $this->items;
    }

    public function scheduleTask(Position $pos, Item $item, Player $player, float $breakTime, int $slot): void
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
            unset($this->handlers[$player->getName()][$this->blockHash($pos)]);
        }), (int)floor($breakTime));
        if (!isset($this->handlers[$player->getName()])) {
            $this->handlers[$player->getName()] = [];
        }
        $this->handlers[$player->getName()][$this->blockHash($pos)] = $handler;
    }

    public function blockHash(Position $pos): string
    {
        return implode(":", [$pos->getFloorX(), $pos->getFloorY(), $pos->getFloorZ(), $pos->getWorld()->getFolderName()]);
    }

    public function stopTask(Player $player, Position $pos): void
    {
        if (!isset($this->handlers[$player->getName()][$this->blockHash($pos)])) {
            return;
        }
        $handler = $this->handlers[$player->getName()][$this->blockHash($pos)];
        $handler->cancel();
        $player->getWorld()->broadcastPacketToViewers($pos, LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 1, $pos->asVector3()));
        unset($this->handlers[$player->getName()][$this->blockHash($pos)]);
    }

    public function getPacket(): ?ItemComponentPacket
    {
        return $this->packet;
    }
}