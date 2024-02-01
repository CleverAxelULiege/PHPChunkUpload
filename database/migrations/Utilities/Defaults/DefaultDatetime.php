<?php

namespace Migrations\Utilities\Defaults;

class DefaultDatetime{
    private const CURRENT_TIMESTAMP = "CURRENT_TIMESTAMP";
    private const CURRENT_TIME = "CURRENT_TIME";
    private const CURRENT_DATE = "CURRENT_DATE";

    public static function getCurrentTimestamp(?int $precision = null){
        return DefaultDatetime::CURRENT_TIMESTAMP . ($precision !== null ? "(".$precision.")" : "");
    }

    public static function getCurrentTime(?int $precision = null){
        return DefaultDatetime::CURRENT_TIME . ($precision !== null ? "(".$precision.")" : "");
    }

    public static function getCurrentDate(){
        return DefaultDatetime::CURRENT_DATE;
    }
}