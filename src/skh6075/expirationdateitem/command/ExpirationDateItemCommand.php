<?php

namespace skh6075\expirationdateitem\command;

use pocketmine\command\Command;
use pocketmine\command\CommandSender;
use pocketmine\player\Player;
use skh6075\expirationdateitem\ExpirationDateItemLoader;
use skh6075\expirationdateitem\lang\PluginLang;

final class ExpirationDateItemCommand extends Command{

    public function __construct() {
        parent::__construct(PluginLang::getInstance()->format("expiration.command.name", [], false), PluginLang::getInstance()->format("expiration.command.description", [], false));
        $this->setPermission("expiration.manage.permission");
    }

    public function execute(CommandSender $player, string $label, array $args): bool{
        if (!$player instanceof Player) {
            $player->sendMessage(PluginLang::getInstance()->format("command.use.only.ingame"));
            return false;
        }

        if (!$this->testPermission($player)) {
            return false;
        }

        if (count($args) < 6) {
            $player->sendMessage(PluginLang::getInstance()->format("expiration.command.help"));
            return false;
        }

        $makeTime = mktime(intval($args[3]), intval($args[4]), intval($args[5]), intval($args[1]), intval($args[2]), intval($args[0]));
        $formatTime = date(PluginLang::getInstance()->format("date.format", [], false), $makeTime);
        if ($makeTime <= time()) {
            $player->sendMessage(PluginLang::getInstance()->format("expiration.out.date", ["%date%" => $formatTime]));
            return false;
        }

        if (($item = $player->getInventory()->getItemInHand())->isNull()) {
            $player->sendMessage(PluginLang::getInstance()->format("expiration.isnull.item"));
            return false;
        }

        $item->getNamedTag()->setInt(ExpirationDateItemLoader::TAG_EXPIRATION, $makeTime);
        $item->setLore($item->getLore() + ["", PluginLang::getInstance()->format("expiration.item.lore", ["%date%" => $formatTime], false)]);
        $player->getInventory()->setItemInHand(clone $item);
        $player->sendMessage(PluginLang::getInstance()->format("expiration.command.success"));
        return true;
    }
}