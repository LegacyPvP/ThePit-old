<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\ArrayShape;
use Legacy\ThePit\Core;
use Legacy\ThePit\Exceptions\FormsException;
use Legacy\ThePit\Forms\element\Button;
use Legacy\ThePit\Forms\element\Input;
use Legacy\ThePit\Forms\Form;
use Legacy\ThePit\Forms\utils\FormResponse;
use Legacy\ThePit\Forms\variant\CustomForm;
use Legacy\ThePit\Forms\variant\SimpleForm;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\FormsUtils;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\player\Player;

abstract class FormsManager
{
    /*#[ArrayShape(["form" => "\Legacy\ThePit\Forms\variant\CustomForm", "callable" => "\Closure[]", "type" => "string"])] static public function knockBackForm(LegacyPlayer $player): Form
    {
        $form = new CustomForm("Knockback", function (Player $player, FormResponse $response): void {
        });
    }*/

    /**
     * @var Form[]
     */
    public static array $forms = [];

    static public function knockBackForm(LegacyPlayer $player): Form {
        $form = new CustomForm("Knock Back", function (Player $player, FormResponse $response): void {
            $horizontal = $response->getInputSubmittedText("horizontal");
            $vertical = $response->getInputSubmittedText("vertical");
            $cooldown = $response->getInputSubmittedText("cooldown");
            if(is_numeric($horizontal) and is_numeric($vertical) and is_numeric($cooldown)) {
                Core::getInstance()->getConfig()->setNested("knockback.horizontal", (float)$horizontal);
                Core::getInstance()->getConfig()->setNested("knockback.vertical", (float)$vertical);
                Core::getInstance()->getConfig()->setNested("knockback.attack_cooldown", (float)$cooldown);
                Core::getInstance()->getConfig()->save();
                throw new FormsException('messages.commands.knockback.success', [
                    "{horizontal}" => (float)$horizontal,
                    "{vertical}" => (float)$vertical,
                    "{attack_cooldown}" => (float)$cooldown
                ], ServerUtils::PREFIX_3);
            }
            throw new FormsException('messages.commands.knockback.invalid-arguments',  [], ServerUtils::PREFIX_2);
        });
        $form->addElement('horizontal', new Input('Horizontal', KnockBackManager::getHorizontal(), '0.40'));
        $form->addElement('vertical', new Input('Vertical', KnockBackManager::getVertical(), '0.40'));
        $form->addElement('cooldown', new Input('Attack Cooldown', KnockBackManager::getAttackCooldown(), '10'));
        return $form;
    }

    static public function shopVotecoins(LegacyPlayer $player): Form {
        $form = new SimpleForm($player->getLanguage()->getMessage("forms.headers.shop-votecoins"), "");
        $form->addButton(new Button($player->getLanguage()->getMessage("forms.buttons.shop-votecoins.conversion-gold")->__toString()));
        $form->addButton(new Button($player->getLanguage()->getMessage("forms.buttons.shop-votecoins.conversion-keys")->__toString()));
        $form->addButton(new Button($player->getLanguage()->getMessage("forms.buttons.shop-votecoins.conversion-boosters")->__toString()));
        return $form;
    }

    static public function openShopVotecoins(LegacyPlayer $player): Form {
        $form = new CustomForm("Boutique de Votecoins", function (Player $player, FormResponse $response): void {
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
        $form = new CustomForm("Boutique de Votecoins", function (Player $player, FormResponse $response): void {
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

    public static function getForms(): array {
        return [
            "knockback" => fn(LegacyPlayer $player) => self::knockBackForm($player),
            "shop-votecoins" => fn(LegacyPlayer $player) => self::shopVotecoins($player),
        ];
    }

    static public function initForms(): void {
        foreach (self::getForms() as $id => $form){
            self::$forms[$id] = $form;
            Core::getInstance()->getLogger()->notice("[FORMS] Form: $id Loaded");
        }
    }

    /**
     * @throws FormsException
     */
    public static function sendForm(LegacyPlayer $player, string $form): void {
        if(isset(self::$forms[$form])){
            $form_infos = array();
            $form = (self::$forms[$form])($player);
            switch($form->getType()){
                case Form::TYPE_CUSTOM_FORM:
                case Form::TYPE_SIMPLE_FORM:
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