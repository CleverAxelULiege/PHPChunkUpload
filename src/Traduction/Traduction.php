<?php

namespace Upload\Traduction;

class Traduction
{
    const DEFAULT = "fr";

    public static function retrieve()
    {
        $lng = $_GET["lng"] ?? Traduction::DEFAULT;

        if (self::existsInFile($lng)) {
            return require(__DIR__ . "/" . $lng . ".php");
        } else {
            return require(__DIR__ . "/" . Traduction::DEFAULT . ".php");
        }
    }

    public static function getLng()
    {
        $lng = $_GET["lng"] ?? Traduction::DEFAULT;

        if (self::existsInFile($lng)) {
            return $lng;
        } else {
            return Traduction::DEFAULT;
        }
    }

    private static function existsInFile(string $lng)
    {
        $traductions = array_filter(
            scandir(__DIR__),
            fn ($file) => is_file(__DIR__ . "/" . $file) && $file !== "Traduction.php"
        );

        return in_array($lng . ".php", $traductions);
    }
}
