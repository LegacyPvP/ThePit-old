<?php

namespace Legacy\ThePit\Objects;

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

}