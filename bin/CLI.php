<?php

namespace bin;


//https://misc.flogisoft.com/bash/tip_colors_and_formatting
class CLI
{
    public const BOLD = "\e[1m";
    public const NORMAL = "\e[0m";
    public const UNDERLINE = "\e[4m";

    public static function success(string $msg, string $textStyle = "")
    {
        echo "\e[32m". $textStyle . $msg . self::backToDefault();
    }

    public static function warning(string $msg, string $textStyle = "")
    {
        echo "\e[33m". $textStyle . $msg . self::backToDefault();
    }

    public static function error(string $msg, string $textStyle = "")
    {
        echo "\e[31m". $textStyle . $msg . self::backToDefault();
    }

    public static function info(string $msg, string $textStyle = "")
    {
        echo "\e[36m". $textStyle . $msg . self::backToDefault();
    }

    public static function default(string $msg, string $textStyle = "")
    {
        echo $textStyle . $msg . self::backToDefault();
    }

    public static function clear(int $times = 5)
    {
        for ($i = 0; $i < $times; $i++) {
            echo "\n";
        }
    }

    private static function backToDefault()
    {
        return "\e[0m";
    }
}
