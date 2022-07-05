<?php

namespace legacy\world\events\listeners;

use legacy\world\Main;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerChatEvent;
use pocketmine\event\player\PlayerCommandPreprocessEvent;
use pocketmine\event\player\PlayerDropItemEvent;
use pocketmine\event\player\PlayerExhaustEvent;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemConsumeEvent;
use pocketmine\player\Player;

final class PlayerListeners implements Listener
{
    private Main $plugin;
    private array $microCache;

    public function __construct(Main $plugin)
    {
        $this->plugin = $plugin;
        $this->microCache = ['name' => []];
    }

    private function getPlugin(): Main
    {
        return $this->plugin;
    }

    public function onHunger(PlayerExhaustEvent $event): void
    {
        $player = $event->getPlayer();
        if (!$player instanceof Player) return;
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($player->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($player->getPosition());
            if (!$flags['hunger']) {
                $event->cancel();
            }
        }
    }

    public function onDrop(PlayerDropItemEvent $event): void
    {
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($player->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($player->getPosition());
            if (!$flags['dropItem']) {
                if ($this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.dropitem.event')) return;
                $event->cancel();
            }
        }
    }


    public function onConsume(PlayerItemConsumeEvent $event): void
    {
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($player->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($player->getPosition());
            if (!$flags['consume']) {
                if ($this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.consume.event')) return;
                $event->cancel();
            }
        }
    }

    public function onCmd(PlayerCommandPreprocessEvent $event): void
    {
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($player->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($player->getPosition());
            if (!$flags['cmd']) {
                if ($this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.cmd.event')) return;
                $event->cancel();
            }
        }
    }

    public function onInteract(PlayerInteractEvent $event): void
    {
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($player->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($player->getPosition());
            if (!$flags['place']) {
                if ($this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.place.event')) return;
                $event->cancel();
            }
        }
    }

    public function onChatSend(PlayerChatEvent $event): void
    {
        $player = $event->getPlayer();
        $api = $this->getPlugin()->getApi();

        if ($api->isInArea($player->getPosition())) {
            $flags = $api->getFlagsAreaByPosition($player->getPosition());
            if (!$flags['chat']) {
                if ($this->getPlugin()->getServer()->isOp($player->getName()) || $player->hasPermission('protectyourspawn.chat.event')) return;
                $event->cancel();
            }
        }
    }

    public function onDamage(EntityDamageByEntityEvent $event): void
    {
        $victim = $event->getEntity();
        $damager = $event->getDamager();
        if ($victim instanceof Player && $damager instanceof Player) {
            $api = $this->getPlugin()->getApi();
            if ($api->isInArea($victim->getPosition())) {
                $flags = $api->getFlagsAreaByPosition($victim->getPosition());
                if (!$flags['pvp']) {
                    $event->cancel();
                }
            }
        }
    }

    /**
     * @return array[]
     */
    public function getMicroCache(): array
    {
        return $this->microCache;
    }
}