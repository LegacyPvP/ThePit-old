<?php

namespace legacy\world\events\listeners;

use legacy\world\commands\Area as CommandArea;
use legacy\world\forms\CustomForm;
use legacy\world\forms\CustomFormResponse;
use legacy\world\forms\elements\Input;
use legacy\world\forms\elements\Toggle;
use legacy\world\Main;
use legacy\world\templates\Area;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\block\BlockPlaceEvent;
use pocketmine\event\Listener;
use pocketmine\player\Player;

final class BlockListeners implements Listener
{
    private Main $plugin;


    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
    }

    private function getPlugin(): Main
    {
        return $this->plugin;
    }

    public function onBreak(BlockBreakEvent $event): void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($block->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($block->getPosition());
            if (!$flags['break'])  {
                if (!$this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.breakblock.event')) {
                    $event->cancel();
                }
            }
        } else {
            $uuid = $player->getUniqueId()->getBytes();
            if (isset(CommandArea::$fastCache[$uuid])) {
                if (is_null(CommandArea::$fastCache[$uuid]['1'])) {
                    CommandArea::$fastCache[$uuid]['1'] = $block->getPosition();
                    $player->sendMessage("§6§l»§r§6 [1] §aLa première position de votre zone est définie.");
                    $event->cancel();
                } elseif (is_null(CommandArea::$fastCache[$uuid]['2'])) {
                    CommandArea::$fastCache[$uuid]['2'] = $block->getPosition();
                    $player->sendMessage("§6§l»§r§6 [2] §aLa deuxième position de votre zone est créée, go à la création de votre zone !");
                    $event->cancel();
                    $player->sendForm(new CustomForm(
                        '§6- §eCréation de la zone §6-',
                        [
                            new Input('§6» §eNom de la zone', 'Spawn'),
                            new Toggle('§6» §ePVP', false),
                            new Toggle('§6» §ePlacing blocks', false),
                            new Toggle('§6» §eBreaking blocks', false),
                            new Toggle('§6» §eStarving', true),
                            new Toggle('§6» §eDrop items', true),
                            new Toggle('§6» §eThe tnt explodes', false),
                            new Toggle('§6» §eCommand [/]', true),
                            new Toggle('§6» §eMessage send in chat', true),
                            new Toggle('§6» §eConsume item', false),
                            new Input ('§6» §ePriority', '0'),
                        ],
                        function (Player $player, CustomFormResponse $response) use ($uuid, $api): void
                        {
                            list($name, $pvp, $place, $break, $hunger, $drop, $tnt, $cmd, $chat, $consume, $prio) = $response->getValues();
                            if (isset($api->cache[$name])) {
                                $player->sendMessage("§4§l»§r§c Le nom de la zone existe déjà !");
                                return;
                            }
                            $flags = Area::createBaseFlags();
                            $flags['pvp'] = $pvp;
                            $flags['place'] = $place;
                            $flags['break'] = $break;
                            $flags['hunger'] = $hunger;
                            $flags['dropItem'] = $drop;
                            $flags['tnt'] = $tnt;
                            $flags['cmd'] = $cmd;
                            $flags['chat'] = $chat;
                            $flags['consume'] = $consume;
                            if (!(int)$prio) {
                                $player->sendMessage("§4§l»§r§c Vous devez spécifier une priorité en nombre !");
                                return;
                            }
                            $area = new Area(CommandArea::$fastCache[$uuid]['1'], CommandArea::$fastCache[$uuid]['2'], $flags, $name, $prio);
                            $this->getPlugin()->getApi()->createArea($area);
                            unset(CommandArea::$fastCache[$uuid]);
                            $player->sendMessage("§4§l»§r§a La zone §6$name §aa été créé avec succès !");
                        }
                    ));
                }
            } elseif (isset(CommandArea::$fastCache[$uuid])) {
                if (is_null(CommandArea::$fastCache[$uuid]['1'])) {
                    CommandArea::$fastCache[$uuid]['1'] = $block->getPosition();
                    $player->sendMessage("§6§l»§r§6 [1 (modified)] §aLa première position de votre zone est définie.");
                    $event->cancel();
                } elseif (is_null(CommandArea::$fastCache[$uuid]['2'])) {
                    CommandArea::$fastCache[$uuid]['2'] = $block->getPosition();
                    $player->sendMessage("§6§l»§r§6 [2 (modified)] §aLa deuxième position de votre zone est définie.");
                    $this->getPlugin()->getApi()->setPositionByName(CommandArea::$fastCache[$uuid]['name'], CommandArea::$fastCache[$uuid]['1'], CommandArea::$fastCache[$uuid]['2']);
                    $player->sendMessage("§6§l»§r§a Les nouvelles positions de la zone §6" . CommandArea::$fastCache[$uuid]['name'] . '§a sont créé !');
                    $event->cancel();
                    unset(CommandArea::$fastCache[$uuid]);
                }
            }
        }
    }

    public function onPlace(BlockPlaceEvent $event): void
    {
        $block = $event->getBlock();
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($block->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($block->getPosition());
            if (!$flags['place'])  {
                if ($this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.placeblock.event')) return;
                $event->cancel();
            }
        }
    }
}