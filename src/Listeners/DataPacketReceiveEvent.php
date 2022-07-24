<?php

namespace Legacy\ThePit\Listeners;

use Exception;
use Legacy\ThePit\Managers\Managers;
use pocketmine\block\BlockFactory;
use pocketmine\block\BlockLegacyIds;
use pocketmine\block\BlockToolType;
use pocketmine\block\ItemFrame;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\server\DataPacketReceiveEvent as ClassEvent;
use pocketmine\item\Axe;
use pocketmine\item\Hoe;
use pocketmine\item\Pickaxe;
use pocketmine\item\Shovel;
use pocketmine\item\Sword;
use pocketmine\math\Vector3;
use pocketmine\network\mcpe\protocol\AnimatePacket;
use pocketmine\network\mcpe\protocol\LevelEventPacket;
use pocketmine\network\mcpe\protocol\LevelSoundEventPacket;
use pocketmine\network\mcpe\protocol\PlayerAuthInputPacket;
use pocketmine\network\mcpe\protocol\types\LevelEvent;
use pocketmine\network\mcpe\protocol\types\LevelSoundEvent;
use pocketmine\network\mcpe\protocol\types\PlayerAction;
use pocketmine\network\mcpe\protocol\types\PlayerBlockActionWithBlockInfo;
use pocketmine\world\Position;

final class DataPacketReceiveEvent implements Listener
{
    public function onEvent(ClassEvent $event): void
    {
        $player = $event->getOrigin()->getPlayer();
        $packet = $event->getPacket();
        switch (true) {
            case $packet instanceof AnimatePacket:
                $event->getOrigin()->getPlayer()->getServer()->broadcastPackets($event->getOrigin()->getPlayer()->getViewers(), [$event->getPacket()]);
                break;
            case $packet instanceof PlayerAuthInputPacket: // Custom Items
                try {
                    $actions = $packet->getBlockActions();
                    if (is_null($actions)) return;

                    foreach ($actions as $action) {
                        if (!$action instanceof PlayerBlockActionWithBlockInfo) return;

                        $pos = new Vector3($action->getBlockPosition()->getX(), $action->getBlockPosition()->getY(), $action->getBlockPosition()->getZ());

                        if ($action->getActionType() === PlayerAction::START_BREAK) {
                            $item = $player->getInventory()->getItemInHand();
                            if (!in_array($item::class, [
                                Pickaxe::class,
                                Axe::class,
                                Shovel::class,
                                Sword::class,
                                Hoe::class
                            ])) {
                                return;
                            }

                            if ($pos->distanceSquared($player->getPosition()) > 10000) {
                                return;
                            }

                            $target = $player->getWorld()->getBlock($pos);

                            $ev = new PlayerInteractEvent($player, $player->getInventory()->getItemInHand(), $target, null, $action->getFace(), PlayerInteractEvent::LEFT_CLICK_BLOCK);
                            if ($player->isSpectator()) {
                                $ev->cancel();
                            }

                            $ev->call();
                            if ($ev->isCancelled()) {
                                $event->getOrigin()->getInvManager()?->syncSlot($player->getInventory(), $player->getInventory()->getHeldItemIndex());
                                return;
                            }

                            $frameBlock = $player->getWorld()->getBlock($pos);
                            if ($frameBlock instanceof ItemFrame && $frameBlock->getFramedItem() !== null) {
                                if (lcg_value() <= $frameBlock->getItemDropChance()) {
                                    $player->getWorld()->dropItem($frameBlock->getPosition(), $frameBlock->getFramedItem());
                                }
                                $frameBlock->setFramedItem(null);
                                $frameBlock->setItemRotation(0);
                                $player->getWorld()->setBlock($pos, $frameBlock);
                                return;
                            }

                            $block = $target->getSide($action->getFace());
                            if ($block->getId() === BlockLegacyIds::FIRE) {
                                $player->getWorld()->setBlock($block->getPosition(), BlockFactory::getInstance()->get(BlockLegacyIds::AIR, 0));
                                return;
                            }

                            $pass = false;
                            if (
                                ($item instanceof Pickaxe && $target->getBreakInfo()->getToolType() === BlockToolType::PICKAXE) ||
                                ($item instanceof Axe && $target->getBreakInfo()->getToolType() === BlockToolType::AXE) ||
                                ($item instanceof Shovel && $target->getBreakInfo()->getToolType() === BlockToolType::SHOVEL) ||
                                $item instanceof Sword ||
                                ($item instanceof Hoe && $target->getBreakInfo()->getToolType() === BlockToolType::HOE)
                            ) $pass = true;


                            if ($pass) {
                                if (!$player->isCreative()) {
                                    $breakTime = ceil($target->getBreakInfo()->getBreakTime($player->getInventory()->getItemInHand()) * 20);
                                    Managers::CUSTOMITEMS()->scheduleTask(Position::fromObject($pos, $player->getWorld()), $player->getInventory()->getItemInHand(), $player, $breakTime, $player->getInventory()->getHeldItemIndex());
                                    $player->getWorld()->broadcastPacketToViewers($pos, LevelSoundEventPacket::nonActorSound(LevelSoundEvent::BREAK_BLOCK, $pos, false));
                                }
                            }
                        } elseif ($action->getActionType() === PlayerAction::ABORT_BREAK) {
                            $player->getWorld()->broadcastPacketToViewers($pos, LevelEventPacket::create(LevelEvent::BLOCK_STOP_BREAK, 0, $pos->asVector3()));
                            Managers::CUSTOMITEMS()->stopTask($player, Position::fromObject($pos, $player->getWorld()));
                        }
                    }
                } catch (Exception) {

                }
        }
    }
}