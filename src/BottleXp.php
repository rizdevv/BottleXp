<?php

use pocketmine\plugin\PluginBase;
use BottleXp\command\XpCommand;

class BottleXp extends PluginBase
{
    public function onEnable() : void
    {
        $this->getServer()->getCommandMap()->register("", new XpCommand());
    }
}
