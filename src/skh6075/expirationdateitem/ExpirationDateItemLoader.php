<?php

namespace skh6075\expirationdateitem;

use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerInteractEvent;
use pocketmine\event\player\PlayerItemHeldEvent;
use pocketmine\item\Item;
use pocketmine\item\ItemFactory;
use pocketmine\nbt\tag\IntTag;
use pocketmine\player\Player;
use pocketmine\plugin\Plugin;
use pocketmine\plugin\PluginBase;
use pocketmine\scheduler\ClosureTask;
use pocketmine\utils\SingletonTrait;
use skh6075\expirationdateitem\command\ExpirationDateItemCommand;
use skh6075\expirationdateitem\lang\PluginLang;

final class ExpirationDateItemLoader extends PluginBase implements Listener{
    use SingletonTrait;

    public const TAG_EXPIRATION = "expirationDate";

    private static PluginLang $lang;

    protected function onLoad(): void{
        self::setInstance($this);
    }

    protected function onEnable(): void{
        $this->saveResource("lang/kor.yml");
        $this->saveResource("lang/eng.yml");
        self::$lang = (new PluginLang())
            ->setName($lang = $this->getServer()->getLanguage()->getLang())
            ->setTranslates(yaml_parse(file_get_contents($this->getDataFolder() . "lang/" . $lang . ".yml")));

        $this->getServer()->getCommandMap()->register(strtolower($this->getName()), new ExpirationDateItemCommand());
        $this->getServer()->getPluginManager()->registerEvents($this, $this);
    }

    public function isOutExpirationDateItem(Item $item): bool{
        if (!$item->getNamedTag()->getTag(self::TAG_EXPIRATION) instanceof IntTag) {
            return false;
        }

        return time() >= $item->getNamedTag()->getInt(self::TAG_EXPIRATION);
    }

    /**
     * @param PlayerItemHeldEvent $event
     *
     * @priority HIGHEST
     */
    public function onPlayerItemHeld(PlayerItemHeldEvent $event): void{
        if ($this->isOutExpirationDateItem($event->getItem())) {
            $player = $event->getPlayer();
            $player->getInventory()->setItemInHand(ItemFactory::air());
            $player->sendMessage(PluginLang::getInstance()->format("expiration.out.date.item"));
        }
    }

    /**
     * @param PlayerInteractEvent $event
     *
     * @priority HIGHEST
     */
    public function onPlayerInteract(PlayerInteractEvent $event): void{
        if ($this->isOutExpirationDateItem($event->getItem())) {
            $player = $event->getPlayer();
            $player->getInventory()->setItemInHand(ItemFactory::air());
            $player->sendMessage(PluginLang::getInstance()->format("expiration.out.date.item"));
        }
    }

    /**
     * @param EntityDamageByEntityEvent $event
     *
     * @priority HIGHEST
     */
    public function onEntityDamage(EntityDamageByEntityEvent $event): void{
        /** @var Player $player */
        if (!($player = $event->getDamager()) instanceof Player)
            return;

        if (!$this->isOutExpirationDateItem($player->getInventory()->getItemInHand()))
            return;
            
        $player->getInventory()->setItemInHand(ItemFactory::air());
        $player->sendMessage(PluginLang::getInstance()->format("expiration.out.date.item"));
    }
}
