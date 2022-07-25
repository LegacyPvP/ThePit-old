<?php

namespace Legacy\ThePit\objects;

use pocketmine\command\CommandSender;

final class Message
{
    public function __construct(private string $message)
    {
    }

    public function __toString(): string
    {
        return $this->message;
    }

    public function send(CommandSender $sender): void
    {
        $sender->sendMessage($this->message);
    }

    public function sendPopup(CommandSender $sender): void
    {
        $sender->sendPopup($this->message);
    }

    public function sendTip(CommandSender $sender): void
    {
        $sender->sendTip($this->message);
    }

    public function sendTitle(CommandSender $sender): void
    {
        $sender->sendTitle($this->message);
    }

    public function sendSubTitle(CommandSender $sender): void
    {
        $sender->sendSubTitle($this->message);
    }
}