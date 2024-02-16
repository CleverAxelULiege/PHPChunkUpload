<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20240206103016_table_target_audiences_country extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("target_audiences_country")->with([
            [
				"country" => "Belgium",
				"survey_question_id" => "1"
            ],
            [
				"country" => "France",
				"survey_question_id" => "1"
            ],
            [
				"country" => "Germany",
				"survey_question_id" => "2"
            ], 
            [
				"country" => "Spain",
				"survey_question_id" => "2"
            ], 
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : here all the columns in the table given, beware some columns may have a default value and be sure to respect the type of the columns.
        ]);
    }
}