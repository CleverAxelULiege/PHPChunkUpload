<?php

namespace Surveys\Traduction;

use UnexpectedValueException;

class Traduction
{
    const DEFAULT = "fr";

    // public static function retrieve()
    // {
    //     $lng = $_GET["lng"] ?? Traduction::DEFAULT;

    //     if (self::existsInFile($lng)) {
    //         return require(__DIR__ . "/" . $lng . ".php");
    //     } else {
    //         return require(__DIR__ . "/" . Traduction::DEFAULT . ".php");
    //     }
    // }

    public static function retrieveSurveyResponseMessage(){
        $lng = $_GET["lng"] ?? Traduction::DEFAULT;

        if (self::existsInFile($lng, __DIR__ . "/survey_response_message/")) {
            return require(__DIR__ . "/survey_response_message/" . $lng . ".php");
        } else {
            return require(__DIR__ . "/survey_response_message/" . Traduction::DEFAULT . ".php");
        }
    }

    public static function getLng()
    {
        $lng = $_GET["lng"] ?? Traduction::DEFAULT;
        return $lng;
        // if (self::existsInFile($lng)) {
        //     return $lng;
        // } else {
        //     return Traduction::DEFAULT;
        // }
    }

    private static function existsInFile(string $lng, string $traductionPath)
    {
        $traductions = array_filter(
            scandir($traductionPath),
            fn ($file) => is_file($traductionPath . $file) && $file !== "Traduction.php"
        );

        if(!in_array(Traduction::DEFAULT . ".php", $traductions)){
            throw new UnexpectedValueException("No default traduction found at the path " . $traductionPath . ". You absolutely need a traduction file as : " . Traduction::DEFAULT . ".php or change the default.");
        }

        return in_array($lng . ".php", $traductions);
    }
}
