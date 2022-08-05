<?php

namespace Legacy\ThePit\managers;

use JetBrains\PhpStorm\ArrayShape;
use Legacy\ThePit\Core;
use Legacy\ThePit\exceptions\FormsException;
use Legacy\ThePit\forms\element\Button;
use Legacy\ThePit\forms\element\Input;
use Legacy\ThePit\forms\element\Slider;
use Legacy\ThePit\forms\Form;
use Legacy\ThePit\forms\utils\FormResponse;
use Legacy\ThePit\forms\variant\CustomForm;
use Legacy\ThePit\forms\variant\SimpleForm;
use Legacy\ThePit\objects\Rank;
use Legacy\ThePit\objects\Sound;
use Legacy\ThePit\player\LegacyPlayer;
use Legacy\ThePit\utils\CurrencyUtils;
use Legacy\ThePit\utils\EquipmentUtils;
use Legacy\ThePit\utils\FormsUtils;
use Legacy\ThePit\utils\ServerUtils;
use pocketmine\player\Player;

final class FormsManager extends Managers
{
    /*#[ArrayShape(["form" => "\Legacy\ThePit\forms\variant\CustomForm", "callable" => "\Closure[]", "type" => "string"])] static public function knockBackForm(LegacyPlayer $player): Form
    {
        $form = new CustomForm("Knockback", function (player $player, FormResponse $response): void {
        });
    }*/

    /**
     * @var Form[]
     */
    public array $forms = [];

    public function knockBackForm(LegacyPlayer $player): Form {
        $form = new CustomForm("Knock Back", function (Player $player, FormResponse $response): void {
            $horizontal = $response->getInputSubmittedText("horizontal");
            $vertical = $response->getInputSubmittedText("vertical");
            $cooldown = $response->getInputSubmittedText("cooldown");
            if(is_numeric($horizontal) and is_numeric($vertical) and is_numeric($cooldown)) {
                Managers::DATA()->get("config")->setNested("knockback.horizontal", (float)$horizontal);
                Managers::DATA()->get("config")->setNested("knockback.vertical", (float)$vertical);
                Managers::DATA()->get("config")->setNested("knockback.attack_cooldown", (float)$cooldown);
                throw new FormsException('messages.commands.knockback.success', [
                    "{horizontal}" => (float)$horizontal,
                    "{vertical}" => (float)$vertical,
                    "{attack_cooldown}" => (float)$cooldown
                ], ServerUtils::PREFIX_3);
            }
            throw new FormsException('messages.commands.knockback.invalid-arguments',  [], ServerUtils::PREFIX_2);
        });
        $form->addElement('horizontal', new Input('Horizontal', Managers::KNOCKBACK()->getHorizontal(), '0.40'));
        $form->addElement('vertical', new Input('Vertical', Managers::KNOCKBACK()->getVertical(), '0.40'));
        $form->addElement('cooldown', new Input('Attack Cooldown', Managers::KNOCKBACK()->getAttackCooldown(), '10'));
        return $form;
    }

    /*#[ArrayShape(["form" => "\Legacy\ThePit\forms\variant\CustomForm", "callable" => "\Closure[]", "type" => "string"])] static public function shopVotecoins(LegacyPlayer $player): Form {
    static public function shopVotecoins(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("forms.headers.shop-votecoins"), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("forms.buttons.shop-votecoins.conversion-gold")->__toString()));
        $form->addButton(new Button($player->getLanguage()->getMessage("forms.buttons.shop-votecoins.conversion-keys")->__toString()));
        $form->addButton(new Button($player->getLanguage()->getMessage("forms.buttons.shop-votecoins.conversion-boosters")->__toString()));
        return $form;
    }

    static public function openShopVotecoins(LegacyPlayer $player): Form {
        $form = new CustomForm("Boutique de Votecoins", function (player $player, FormResponse $response): void {
            if($player instanceof LegacyPlayer){
                $button_convert = $player->getLanguage()->getMessage("messages.forms.shop.votecoins-convert", [], ServerUtils::PREFIX_3);
            }
            throw new FormsException('messages.commands.knockback.invalid-arguments',  [], ServerUtils::PREFIX_2);
        });

        $form->addElement('', new Button("Conversion en or"));
        $form->addElement('', new Button("Conversion en boosters"));
        $form->addElement('', new Button("Conversion en clés"));
        return $form;
    }

    static public function openShopVotecoinsC(): Form {
        $form = new CustomForm("Boutique de Votecoins", function (player $player, FormResponse $response): void {
            if($player instanceof LegacyPlayer){
                $button_convert = $player->getLanguage()->getMessage("messages.forms.shop.votecoins-convert", [], ServerUtils::PREFIX_3);
            }
            throw new FormsException('messages.commands.knockback.invalid-arguments',  [], ServerUtils::PREFIX_2);
        });

        $form->addElement('', new Button("Conversion en or"));
        $form->addElement('', new Button("Conversion en boosters"));
        $form->addElement('', new Button("Conversion en clés"));
        return $form;
    }*/

    public function equipmentForm(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.equipment", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.equipment.armor", [], false), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                Managers::FORMS()->sendForm($player, "equipment-armor");
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.equipment.weapons", [], false), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                Managers::FORMS()->sendForm($player, "equipment-weapons");
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.equipment.support", [], false), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                Managers::FORMS()->sendForm($player, "equipment-support");
            }
        }));
        return $form;
    }

    public function equipmentArmorForm(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.equipment", [], false), "");
        $form->addButton(new Button(str_replace("{rank}", $player->getArmorLevel(EquipmentUtils::HELMET), $player->getArmor(EquipmentUtils::HELMET)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 600)){
                    if($player->getArmorLevel(EquipmentUtils::HELMET) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 600);
                        $player->upgradeArmor(EquipmentUtils::HELMET);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getArmorLevel(EquipmentUtils::CHESTPLATE), $player->getArmor(EquipmentUtils::CHESTPLATE)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 600)){
                    if($player->getArmorLevel(EquipmentUtils::CHESTPLATE) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 600);
                        $player->upgradeArmor(EquipmentUtils::CHESTPLATE);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getArmorLevel(EquipmentUtils::LEGGINGS), $player->getArmor(EquipmentUtils::LEGGINGS)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 600)){
                    if($player->getArmorLevel(EquipmentUtils::LEGGINGS) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 600);
                        $player->upgradeArmor(EquipmentUtils::LEGGINGS);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getArmorLevel(EquipmentUtils::BOOTS), $player->getArmor(EquipmentUtils::BOOTS)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 600)){
                    if($player->getArmorLevel(EquipmentUtils::BOOTS) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 600);
                        $player->upgradeArmor(EquipmentUtils::BOOTS);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        return $form;
    }

    public function equipmentWeaponsForm(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.equipment", [], false), "");
        $form->addButton(new Button(str_replace("{rank}", $player->getWeaponLevel(EquipmentUtils::SWORD), $player->getWeapon(EquipmentUtils::SWORD)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1000)){
                    if($player->getWeaponLevel(EquipmentUtils::SWORD) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1000);
                        $player->upgradeWeapon(EquipmentUtils::SWORD);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getWeaponLevel(EquipmentUtils::BOW), $player->getWeapon(EquipmentUtils::BOW)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1500)){
                    if($player->getWeaponLevel(EquipmentUtils::BOW) < 2){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1500);
                        $player->upgradeWeapon(EquipmentUtils::BOW);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getWeaponLevel(EquipmentUtils::ARROW), $player->getWeapon(EquipmentUtils::ARROW)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1000)){
                    if($player->getWeaponLevel(EquipmentUtils::ARROW) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1000);
                        $player->upgradeWeapon(EquipmentUtils::ARROW);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        return $form;
    }

    public function equipmentSupportsForm(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.equipment", [], false), "");
        $form->addButton(new Button(str_replace("{rank}", $player->getSupportLevel(EquipmentUtils::HOOK), $player->getSupport(EquipmentUtils::HOOK)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1500)){
                    if($player->getSupportLevel(EquipmentUtils::HOOK) < 1){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1500);
                        $player->upgradeSupport(EquipmentUtils::HOOK);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getSupportLevel(EquipmentUtils::BUCKET_LAVA), $player->getSupport(EquipmentUtils::BUCKET_LAVA)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1500)){
                    if($player->getSupportLevel(EquipmentUtils::BUCKET_LAVA) < 1){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1500);
                        $player->upgradeSupport(EquipmentUtils::BUCKET_LAVA);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getSupportLevel(EquipmentUtils::SNOWBALL), $player->getSupport(EquipmentUtils::SNOWBALL)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1000)){
                    if($player->getSupportLevel(EquipmentUtils::SNOWBALL) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1000);
                        $player->upgradeSupport(EquipmentUtils::SNOWBALL);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getSupportLevel(EquipmentUtils::BLOCKS), $player->getSupport(EquipmentUtils::BLOCKS)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1000)){
                    if($player->getSupportLevel(EquipmentUtils::BLOCKS) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1000);
                        $player->upgradeSupport(EquipmentUtils::BLOCKS);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getSupportLevel(EquipmentUtils::FLAP), $player->getSupport(EquipmentUtils::FLAP)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1000)){
                    if($player->getSupportLevel(EquipmentUtils::FLAP) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1000);
                        $player->upgradeSupport(EquipmentUtils::FLAP);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        $form->addButton(new Button(str_replace("{rank}", $player->getSupportLevel(EquipmentUtils::NEMO), $player->getSupport(EquipmentUtils::NEMO)), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                if($player->getCurrencyProvider()->has(CurrencyUtils::GOLD, 1000)){
                    if($player->getSupportLevel(EquipmentUtils::NEMO) < 3){
                        $player->getCurrencyProvider()->remove(CurrencyUtils::GOLD, 1000);
                        $player->upgradeSupport(EquipmentUtils::NEMO);
                        $player->setStuff();
                        $sound = new Sound("random.pop", 1);
                        $sound->play($player);
                    } else {
                        $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.max", [], ServerUtils::PREFIX_3));
                    }
                }else{
                    $player->sendMessage($player->getLanguage()->getMessage("messages.form.equipment.not-enough", [], ServerUtils::PREFIX_3));
                }
            }
        }));
        return $form;
    }

    public function shop(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.gold"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.votecoins"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.credits"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $this->shopCredits($player);
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.stars"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.upgrade-stuff"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $this->equipmentForm($player);
            }
        }));

        return $form;
    }

    public function shopVotecoins(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.votecoins.convert-to-gold"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.votecoins.convert-to-keys"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.votecoins.convert-to-boosters"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));

        return $form;
    }

    public function shopVotecoinsConvertToGold(LegacyPlayer $player): Form {
        $form = new CustomForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false));
        $form->addElement(1, new Slider($player->getLanguage()->getMessage("messages.form.shop.votecoins.convert-to-gold.slider"), 1, 100, 1));

        return $form;
    }

    public function shopVotecoinsConvertToKeys(LegacyPlayer $player): Form {
        $form = new CustomForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false));


        return $form;
    }

    public function shopVotecoinsConvertToBoosters(LegacyPlayer $player): Form {
        $form = new CustomForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false));


        return $form;
    }

    public function shopCredits(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.credits.rank"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $this->shopCreditsRanks($player);
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.credits.boosters"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $this->shopCreditsBoosters($player);
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.credits.keys"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $this->shopCreditsKeys($player);
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.shop.credits.packs"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $this->shopCreditsPacks($player);
            }
        }));

        return $form;
    }

    public function shopCreditsRanks(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.rank.plus"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $rank = Managers::RANKS()->get("plus");
                $player->getPlayerProperties()->setNestedProperties("infos.rank", $rank->getName());
            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.rank.star"), null, function (Player $player){
            if($player instanceof LegacyPlayer){
                $rank = Managers::RANKS()->get("star");
                $player->getPlayerProperties()->setNestedProperties("infos.rank", $rank->getName());
            }
        }));

        return $form;
    }

    public function shopCreditsBoosters(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.rank.plus"), null, function (Player $player){

        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.rank.star"), null, function (Player $player){

        }));

        return $form;
    }

    public function shopCreditsKeys(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.keys.common"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.keys.rare"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.keys.legendary"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.keys.mythic"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));

        return $form;
    }

    public function shopCreditsPacks(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("messages.form.titles.shop", [], false), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.packs.plus"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.packs.star"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.packs.keys"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));
        $form->addButton(new Button($player->getLanguage()->getMessage("messages.form.packs.cosmetics"), null, function (Player $player){
            if($player instanceof LegacyPlayer){

            }
        }));

        return $form;
    }

    #[ArrayShape(["knockback" => "\Closure", "equipment" => "\Closure", "equipment-armor" => "\Closure"])] public function getAll(): array {
        return [
            "knockback" => fn(LegacyPlayer $player) => $this->knockBackForm($player),
            "equipment" => fn(LegacyPlayer $player) => $this->equipmentForm($player),
            "equipment-armor" => fn(LegacyPlayer $player) => $this->equipmentArmorForm($player),
            "equipment-weapons" => fn(LegacyPlayer $player) => $this->equipmentWeaponsForm($player),
            "equipment-support" => fn(LegacyPlayer $player) => $this->equipmentSupportsForm($player),
            "shop-credits" => fn(LegacyPlayer $player) => $this->shopCredits($player),
            "shop-credits-ranks" => fn(LegacyPlayer $player) => $this->shopCreditsRanks($player),
            "shop-credits-boosters" => fn(LegacyPlayer $player) => $this->shopCreditsBoosters($player),
            "shop-credits-keys" => fn(LegacyPlayer $player) => $this->shopCreditsKeys($player),
            "shop-credits-packs" => fn(LegacyPlayer $player) => $this->shopCreditsPacks($player),
            "shop" => fn(LegacyPlayer $player) => $this->shop($player),
        ];
    }

    public function init(): void {
        foreach ($this->getAll() as $id => $form){
            $this->forms[$id] = $form;
            Core::getInstance()->getLogger()->notice("[FORMS] Form: $id Loaded");
        }
    }

    public function get(string $name): ?Form {
        return isset($this->forms[$name]) ? ($this->forms[$name]) : null; 
    }

    /**
     * @throws FormsException
     */
    public function sendForm(LegacyPlayer $player, string $form): void {
        if(isset($this->forms[$form])){
            $form_infos = array();
            $form = ($this->forms[$form])($player);
            switch($form->getType()){
                case Form::TYPE_SIMPLE_FORM:
                    $form_infos["callable"][] = $form->getCloseListener();
                    $form->setCloseListener(reset($form_infos["callable"]) ?? null);
                    foreach ($form->getButtons() as $id => $button){
                        $form_infos["buttons"][] = $button;
                    }
                    $form->buttons = [];
                    break;
                case Form::TYPE_CUSTOM_FORM:
                    $form_infos["callable"][] = $form->getSubmitListener();
                    $form_infos["callable"][] = $form->getCloseListener();
                    $form->setSubmitListener(reset($form_infos["callable"]) ?? null);
                    $form->setCloseListener(end($form_infos["callable"]) ?? null);
                    break;
                case Form::TYPE_MODAL_FORM:
                    $form_infos["callable"][] = $form->getAcceptOption()->getSubmitListener();
                    $form_infos["callable"][] = $form->getDenyOption()->getSubmitListener();
                    $form->setAcceptListener(reset($form_infos["callable"]) ?? null);
                    $form->setDenyListener(end($form_infos["callable"]) ?? null);
            }
            $form_infos["form"] = $form;
            $form_infos["type"] = $form->getType();
            FormsUtils::sendForm($player, $form_infos);
        } else throw new FormsException("messages.forms.not-found", [
            "{form}" => $form
        ], ServerUtils::PREFIX_2);
    }
}