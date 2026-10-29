<?php

namespace BottleXp\command;

use pocketmine\command\Command;
use pocketmine\permission\PermissionManager;
use pocketmine\permission\DefaultPermissions;
use pocketmine\permission\Permission;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use pocketmine\item\VanillaItems;
use pocketmine\world\sound\XpLevelUpSound;
use pocketmine\world\sound\AnvilFallSound;

class XpCommand extends Command
{
    public const COMMAND_PERMISSION_XP = "command.permission.xp";

    public function __construct()
    {
        parent::__construct("xp", "Обменять опыт на пузырек опыта");
        $root = PermissionManager::getInstance()->getPermission(DefaultPermissions::ROOT_USER);
        DefaultPermissions::registerPermission(new Permission(self::COMMAND_PERMISSION_XP), [$root]);
        $this->setPermission(self::COMMAND_PERMISSION_XP);
    }

    public function execute(CommandSender $sender, string $commandLabel, array $args) : void
    {
        if ($sender instanceof Player)
        {
            $world = $sender->getWorld();
            $location = $sender->getLocation();
            if (isset($args[0]) and is_numeric($args[0]) and (int) $args[0] == $args[0] and $args[0] > 0)
            {
                $xpManager = $sender->getXpManager();
                $xpLevel = $xpManager->getXpLevel();
                if ($args[0] <= $xpLevel)
                {
                    $inventory = $sender->getInventory();
                    $experienceBottle = VanillaItems::EXPERIENCE_BOTTLE()->setCount($args[0]);
                    if ($inventory->canAddItem($experienceBottle))
                    {
                        $xpManager->setXpAndProgress($xpLevel - $args[0], $xpManager->getXpProgress());
                        $inventory->addItem($experienceBottle);
                        $sender->sendMessage("Вы обменяли опыт на§9 пузырек опыта§f, в количестве§9 " . $args[0] . "§f шт.");
                        $world->addSound($location, new XpLevelUpSound(5));
                        return;
                    } else
                    {
                        $sender->sendMessage("§cНедостаточно мест в инвентаре");
                    }
                } else
                {
                    $sender->sendMessage("§cУ вас недостаточно опыта");
                }
            } else
            {
                $sender->sendMessage("Использование:§9 /xp §f<§9целое число: количество§f>");
            }
            $world->addSound($location, new AnvilFallSound());
        } else
        {
            $sender->sendMessage("§cИспользуйте только в игре");
        }
    }
}
