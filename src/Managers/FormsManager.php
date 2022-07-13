<?php

namespace Legacy\ThePit\Managers;

use JetBrains\PhpStorm\ArrayShape;
use Legacy\ThePit\Core;
use Legacy\ThePit\Exceptions\FormsException;
use Legacy\ThePit\Forms\element\Input;
use Legacy\ThePit\Forms\Form;
use Legacy\ThePit\Forms\utils\FormResponse;
use Legacy\ThePit\Forms\variant\CustomForm;
use Legacy\ThePit\Player\LegacyPlayer;
use Legacy\ThePit\Utils\FormsUtils;
use Legacy\ThePit\Utils\ServerUtils;
use pocketmine\player\Player;

abstract class FormsManager
{
    static public function knockBackForm(): Form {
        var_dump(Core::getInstance()->getConfig()->getNested("knockback.horizontal"), KnockBackManager::getHorizontal());
        $form = new CustomForm("Knock Back", function (Player $player, FormResponse $response): void {
            $horizontal = $response->getInputSubmittedText("horizontal");
            $vertical = $response->getInputSubmittedText("vertical");
            $cooldown = $response->getInputSubmittedText("cooldown");
            try {
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
            }
            catch (FormsException $e) {
                $player->getLanguage()->getMessage($e->getMessage(), $e->getArgs(), $e->getPrefix())->send($player);
            }
        });
        $form->addElement('horizontal', new Input('Horizontal', KnockBackManager::getHorizontal(), '0.40'));
        $form->addElement('vertical', new Input('Vertical', KnockBackManager::getVertical(), '0.40'));
        $form->addElement('cooldown', new Input('Attack Cooldown', KnockBackManager::getAttackCooldown(), '10'));
        return $form;
    }

    /**
     * @var Form[]
     */
    public static array $forms = [];

    #[ArrayShape(["knockback" => "\Legacy\ThePit\Forms\Form"])] public static function getForms(): array {
        return [
            "knockback" => fn() => self::knockBackForm(),
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
            FormsUtils::sendForm($player, (self::$forms[$form])());
        } else throw new FormsException("messages.forms.not-found", [
            "{form}" => $form
        ], ServerUtils::PREFIX_2);
    }
}