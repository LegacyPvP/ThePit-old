# WorldProtector
- --
## Les Permissions du plugin: 
```PHP
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
```


## Les commandes du plugin: 
``/CommandArea | Crée une zone de protection.``<br>
``/CommandArea | Liste des zones protégées.``

## Utiliser l'API dans les autres plugins de legacy
```PHP
private ?AreaManager $api = null;

$api = $this->getServer()->getPluginManager()->getPlugin('WorldProtector');
if (!is_null($api)) $this->api = $api->getApi();
```

## Les functions utiles 
```PHP
$flags = $this->api->getFlagsByName($name);
$flags = $this->api->getFlagsAreaByPosition($pos);
$nameOfArea = $this->api->getNameAreaByPosition($pos);
$isInArea = $this->api->isInArea($pos);
$this->api->deleteAreaByName($name);
$nameOfAreaPriority = $this->api->getPriorityByAreaName($name);
```