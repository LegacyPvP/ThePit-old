<?php

namespace Legacy\ThePit\managers;

use JetBrains\PhpStorm\ArrayShape;
use Legacy\ThePit\Core;
use Legacy\ThePit\exceptions\FormsException;
use Legacy\ThePit\forms\element\Input;
use Legacy\ThePit\forms\Form;
use Legacy\ThePit\forms\utils\FormResponse;
use Legacy\ThePit\forms\variant\CustomForm;
use Legacy\ThePit\player\LegacyPlayer;
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

    }

    #[ArrayShape(["knockback" => "\Closure"])] public function getAll(): array {
        return [
            "knockback" => fn(LegacyPlayer $player) => $this->knockBackForm($player),
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