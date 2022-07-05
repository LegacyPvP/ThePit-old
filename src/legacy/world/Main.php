<?php

namespace legacy\world;

use JsonException;
use legacy\world\commands\Area;
use legacy\world\events\listeners\BlockListeners;
use legacy\world\events\listeners\EntityListeners;
use legacy\world\events\listeners\PlayerListeners;
use legacy\world\manager\AreaManager;
use pocketmine\permission\Permission;
use pocketmine\permission\PermissionManager;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Main extends PluginBase
{
    use SingletonTrait;
    public array $notification = [];

    /** @var AreaManager|null  */
    private ?AreaManager $api = null;

    protected function onEnable(): void
    {
        $this::setInstance($this);
        $this->saveDefaultConfig();
        @mkdir($this->getDataFolder() . 'temp/');
        $this->notification = $this->getConfig()->get('notification', []);

        $permissions = [
            'protectyourspawn.create.cmd',
            'protectyourspawn.list.cmd',
            'protectyourspawn.dropitem.event',
            'protectyourspawn.chat.event',
            'protectyourspawn.cmd.event',
            'protectyourspawn.breakblock.event',
            'protectyourspawn.placeblock.event',
            'protectyourspawn.consume.event'
        ];
        foreach ($permissions as $permission)PermissionManager::getInstance()->addPermission(new Permission($permission));

        $events = [
            new BlockListeners($this),
            new EntityListeners($this),
            new PlayerListeners($this)
        ];
        foreach ($events as $event) $this->getServer()->getPluginManager()->registerEvents($event, $this);

        $this->getServer()->getCommandMap()->registerAll('WorldProtector', [
            new Area($this, 'area', 'Ouvrir le menu des zones protégées', '/area')
        ]);


        $this->api = new AreaManager($this);
        parent::onEnable();
    }

    /**
     * @throws JsonException
     */
    protected function onDisable(): void
    {
        $this->getApi()->saveAllData();
        parent::onDisable();
    }

    /**
     * @return AreaManager|null
     */
    public function getApi(): ?AreaManager
    {
        return $this->api;
    }
}