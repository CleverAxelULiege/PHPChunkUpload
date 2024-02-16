<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20240206101716_table_survey_questions extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("survey_questions")->with([
            [
				"question_fr" => "Quand est-ce la dernière fois que vous avez réalisé un salto arrière suivi d'une roulade sur le capot d'une voiture ?",
            ],
            [
				"question_fr" => "Quand est-ce la dernière fois que vous avez essayé de changer l'huile de votre friteuse ?",
            ],
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : here all the columns in the table given, beware some columns may have a default value and be sure to respect the type of the columns.
        ]);
    }
}