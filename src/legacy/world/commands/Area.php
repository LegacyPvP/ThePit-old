<?php

namespace legacy\world\commands;

use legacy\world\forms\CustomForm;
use legacy\world\forms\CustomFormResponse;
use legacy\world\forms\elements\Button;
use legacy\world\forms\elements\Image;
use legacy\world\forms\elements\Toggle;
use legacy\world\forms\MenuForm;
use legacy\world\forms\ModalForm;
use legacy\world\Main;
use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\event\Listener;
use pocketmine\lang\Translatable;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;

final class Area extends Command implements Listener
{
    private Main $plugin;

    /** @var array  */
    public static array $fastCache = [];

    /**
     * CommandArea constructor.
     * @param Main $plugin
     * @param string $name
     * @param Translatable|string $description
     * @param Translatable|string|null $usageMessage
     * @param array $aliases
     */
    public function __construct(Main $plugin, string $name, Translatable|string $description = "", Translatable|string|null $usageMessage = null, array $aliases = [])
    {
        $this->plugin = $plugin;
        parent::__construct($name, $description, $usageMessage, $aliases);
    }

    /**
     * @param CommandSender $sender
     * @param string $commandLabel
     * @param array $args
     * @return void
     */
    public function execute(CommandSender $sender, string $commandLabel, array $args)
    {
        if (!$this->getOwningPlugin()->getServer()->isOp($sender->getName())) {
            if (!$sender->hasPermission('protectyourspawn.list.cmd')) {
                $sender->sendMessage("§4§l»§r §cTu n'as pas la permission de faire cette commande.");
                return;
            }
        }

        if (!$sender instanceof Player) {
            $sender->sendMessage("§4§l»§r §cLa commande s'exécute uniquement en jeu.");
            return;
        }

        switch($args[0] ?? "list"){
            case "l":
            case "list":
                $array = $this->getOwningPlugin()->getApi()->cache;
                $buttons = [];
                $arrayValue = [];
                $i = 0;
                foreach ($array as $name => $values) {
                    $buttons[] = new Button('§c- §6' . $name . ' §c-', new Image('textures/items/apple'));
                    $arrayValue[$i] = $name;
                    $i++;
                }

                $sender->sendForm(new MenuForm(
                    '§6- §eListe des zones §6-',
                    '§7Voici la liste des zones présentes sur le serveur, vous pouvez modifier leurs atributs !',
                    $buttons,
                    function (Player $player, Button $button) use($arrayValue) : void {
                        $value = $button->getValue();
                        $name = $arrayValue[$value];
                        $player->sendForm(new MenuForm(
                            "§6- §eCaratéristique de la zone §c$name §6-",
                            '§7Vous pouvez modifier les attributs de cette zone si vous le souhaitez.',
                            [
                                new Button('§aModifier les attributs', new Image('textures/items/carrot')),
                                new Button('§eRedéfinir les coordonnées', new Image('textures/items/compass_item')),
                                new Button('§cSupprimer la zone [§4/!\§c]', new Image('textures/items/flint_and_steel'))
                            ],
                            function (Player $player, Button $button) use ($name): void
                            {
                                $value = $button->getValue();
                                switch ($value) {
                                    case 0:
                                        $flags = $this->getOwningPlugin()->getApi()->getFlagsByName($name);
                                        $player->sendForm(new CustomForm(
                                            "§6- §eModification de la zone §c$name §6-",
                                            [
                                                new Toggle('§6» §ePVP', $flags['pvp']),
                                                new Toggle('§6» §ePlacing blocks', $flags['place']),
                                                new Toggle('§6» §eBreaking blocks', $flags['break']),
                                                new Toggle('§6» §eStarving', $flags['hunger']),
                                                new Toggle('§6» §eDrop items', $flags['dropItem']),
                                                new Toggle('§6» §eThe tnt explodes', $flags['tnt']),
                                                new Toggle('§6» §eCommand [/]', $flags['cmd']),
                                                new Toggle('§6» §eMessage send in chat', $flags['chat']),
                                                new Toggle('§6» §eConsume item', $flags['consume']),
                                            ],
                                            function (Player $player, CustomFormResponse $response) use ($name): void
                                            {
                                                list($pvp, $place, $break, $hunger, $drop, $tnt, $cmd, $chat, $consume) = $response->getValues();
                                                $flags = \legacy\world\templates\Area::createBaseFlags();
                                                $flags['pvp'] = $pvp;
                                                $flags['place'] = $place;
                                                $flags['break'] = $break;
                                                $flags['hunger'] = $hunger;
                                                $flags['dropItem'] = $drop;
                                                $flags['tnt'] = $tnt;
                                                $flags['cmd'] = $cmd;
                                                $flags['chat'] = $chat;
                                                $flags['consume'] = $consume;
                                                $this->getOwningPlugin()->getApi()->setFlagsByName($name, $flags);
                                                $player->sendMessage("§6§l»§r§a La zone §6$name §aa été modifié avec succès !");
                                            }
                                        ));
                                        break;
                                    case 1:
                                        $uuid = $player->getUniqueId()->getBytes();
                                        if (!isset(self::$fastCache[$uuid])) {
                                            self::$fastCache[$uuid] = ['1' => null, '2' => null, 'name' => $name];
                                        } else {
                                            unset(self::$fastCache[$uuid]);
                                            $player->sendMessage("§c§l»§r Vous avez annulé votre action !");
                                        }
                                        break;
                                    case 2:
                                        $player->sendForm(new ModalForm(
                                            '§4- §cAttention !§c -',
                                            "§cSi vous acceptez la suppression de votre zone, il n’y a aucun moyen de récupérer les données de celui-ci !",
                                            function (Player $player, bool $response) use ($name) : void
                                            {
                                                if ($response) {
                                                    $this->getOwningPlugin()->getApi()->deleteAreaByName($name);
                                                    $player->sendMessage("§6§l»§r§a Vous avez supprimé la zone §6$name §a!");
                                                } else $player->sendMessage("§6§l»§r§a Vous avez annulé la suppression de la zone 6$name §a!");
                                            }
                                        ));
                                        break;
                                }
                            }
                        ));
                    }
                ));
                break;
            case "c":
            case "create":
            default:
                $uuid = $sender->getUniqueId()->getBytes();
                if (!isset(self::$fastCache[$uuid])) {
                    self::$fastCache[$uuid] = ['1' => null, '2' => null];
                    $sender->sendMessage("§6§l»§r §aVeuillez casser deux blocs pour définir les coordonnées de la zone protégée.");
                } else {
                    unset(self::$fastCache[$uuid]);
                    $sender->sendMessage("§4§l»§r §cVous avez annulé la création de la zone protégée.");
                }
        }
    }

    /**
     * @return Plugin
     */
    public function getOwningPlugin(): Plugin
    {
        return $this->plugin;
    }
}