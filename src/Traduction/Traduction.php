<?php

namespace Surveys\Traduction;

use Surveys\DTOs\TraductionDTO;
use UnexpectedValueException;

class Traduction
{
    const DEFAULT = "fr";

    public static function retrieveSurveyResponseMessage(){
        $lng = $_GET["lng"] ?? Traduction::DEFAULT;

        if (self::existsInFile($lng, __DIR__ . "/survey_response_message/")) {
            return new TraductionDTO($lng, require(__DIR__ . "/survey_response_message/" . $lng . ".php"));
        } else {
            return new TraductionDTO(Traduction::DEFAULT, require(__DIR__ . "/survey_response_message/" . Traduction::DEFAULT . ".php"));
        }
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
