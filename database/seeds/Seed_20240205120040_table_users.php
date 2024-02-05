<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20240205120040_table_users extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("users")->with([
            [
				"username" => "admin",
				"password" => "admin",
				"roles" => json_encode([1,2,3,4])
            ]
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : here all the columns in the table given, beware some columns may have a default value and be sure to respect the type of the columns.
        ]);
    }
}