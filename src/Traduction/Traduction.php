<?php

namespace Upload\Traduction;

class Traduction
{
    const DEFAULT = "en";

    public static function retrieve(){
        $lng = $_GET["lng"] ?? Traduction::DEFAULT;

        if(self::exists($lng)){
            return require(__DIR__ . "/" . $lng . ".php");
        } else {
            return require(__DIR__ . "/" . Traduction::DEFAULT . ".php");
        }
    }

    private static function exists(string $lng)
    {
        $traductions = array_filter(
            scandir(__DIR__), 
            fn ($file) => is_file(__DIR__ . "/" . $file) && $file !== "Traduction.php"
        );

        return in_array($lng . ".php", $traductions);
    }
}
