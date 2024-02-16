<?php

namespace Seeds;

use Seeds\Utilities\AbstractSeed;
use Seeds\Utilities\SeedInterface;

class Seed_20240206101235_table_users extends AbstractSeed
{
    public function seed(SeedInterface $seed)
    {
        $seed->table("users")->with([
            [
				"username" => "Billy",
				"password" => "1233456",
				"birthdate" => "2000-12-25",
				"country" => "Belgium",
            ],
            [
				"username" => "Marcel",
				"password" => "1233456",
				"birthdate" => "1995-01-01",
				"country" => "Belgium",
            ], 
            [
				"username" => "Bobby",
				"password" => "1233456",
				"birthdate" => "1975-03-15",
				"country" => "France",
            ], 
            [
				"username" => "Angela",
				"password" => "1233456",
				"birthdate" => "1989-07-09",
				"country" => "Germany",
            ], 
            [
				"username" => "François",
				"password" => "1233456",
				"birthdate" => "2001-05-29",
				"country" => "France",
            ], 
            [
				"username" => "Angelo",
				"password" => "1233456",
				"birthdate" => "1969-09-25",
				"country" => "Spain",
            ], 
            [
				"username" => "La chancla",
				"password" => "1233456",
				"birthdate" => "1982-06-19",
				"country" => "Spain",
			],
            [
				"username" => "Young-boi",
				"password" => "1233456",
				"birthdate" => "2015-02-03",
				"country" => "Switzerland",
            ]
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : here all the columns in the table given, beware some columns may have a default value and be sure to respect the type of the columns.
        ]);
    }
}