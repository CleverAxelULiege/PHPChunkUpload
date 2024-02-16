<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20240206102041_table_target_audiences_age extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("target_audiences_age")->with([
            [
				"min_age" => "18",
				"max_age" => "30",
				"survey_question_id" => "1"
            ],
            [
				"min_age" => "0",
				"max_age" => "100",
				"survey_question_id" => "2"
            ], 
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : here all the columns in the table given, beware some columns may have a default value and be sure to respect the type of the columns.
        ]);
    }
}