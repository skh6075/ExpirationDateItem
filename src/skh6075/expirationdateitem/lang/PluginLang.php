<?php

namespace skh6075\expirationdateitem\lang;

use pocketmine\utils\SingletonTrait;
use function str_replace;

final class PluginLang{
    use SingletonTrait;

    private string $name;

    private array $translates = [];

    public function __construct() {
        self::setInstance($this);
    }

    public function setName(string $name): self{
        $this->name = $name;
        return $this;
    }

    public function setTranslates(array $translates = []): self{
        $this->translates = $translates;
        return $this;
    }

    public function format(string $key, array $replaces = [], bool $pushPrefix = true): string{
        $format = $pushPrefix ? $this->translates["prefix"] ?? "" : "";
        $format .= $this->translates[$key] ?? "";
        foreach ($replaces as $old => $new) {
            $format = str_replace($old, $new, $format);
        }

        return $format;
    }
}