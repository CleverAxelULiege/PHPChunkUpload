<?php

use bin\CLI;

require(__DIR__ . "/CLI.php");

$action = trim($argv[1]) ?? "";

if (str_starts_with($action, "make:")) { //souhaite créer un nouveau seed
    $seedName = substr($action, 5);
    echo passthru("php database/seeds/commands/create.php " . $seedName);
} else if (str_starts_with($action, "seed:")) { //souhaite réalider des actions par rapport aux fichiers de seed
    $seedAction = substr($action, 5);

    if (preg_match("/(\[)((\s?\d*,*)*)(\])/", $seedAction) != 0) { // si un "tableau" a été passé pour ne sélectionner que des index en particulier
        echo passthru("php database/seeds/commands/seed.php --only:" . $seedAction);
    } else {
        switch ($seedAction) {
            case "all": //souhaite seed tous les fichiers
                echo passthru("php database/seeds/commands/seed.php --all");
                break;
            case "list": //souhaite voir tous les fichiers seed
                echo passthru("php database/seeds/commands/seed.php --list");
                break;
        }
    }

}else if(str_starts_with($action, "truncate:")){
    echo passthru("php database/seeds/commands/seed.php --".$action);
} 
else if($action == "help"){
    CLI::clear(15);
    listCommands();
} else {
    CLI::clear(15);
    listCommands(); 
    CLI::warning("Unknown command. Check above to see the list of commands available.\n");
}

function listCommands(){
    CLI::warning("Here are the possible commands for the seeding :\n");
    CLI::info("php bin/seed.php make:<your_seeding_name>\n");
    CLI::default("\t-> Example : php bin/seed.php make:table_users will create a seeding file for a table with the name users.\n");
    CLI::default("\t INFO : If a table is found with the name that you passed after \"table_\", you'll get all the columns of the said table as an associative array, \n", CLI::BOLD);
    CLI::default("\t        else you'll get a dummy associative array.\n", CLI::BOLD);

    CLI::default("\n--------------------------\n");

    CLI::info("\nphp bin/seed.php seed:all\n");
    CLI::default("\t-> Will use all seeding files found.\n");

    CLI::info("\nphp bin/seed.php seed:list\n");
    CLI::default("\t-> Will list all seeding files.\n");

    CLI::info("\nphp bin/seed.php seed:<array>\n");
    CLI::default("\t-> Will only use the seeding files that you passed in the array.\n");
    CLI::default("\t-> To select a file use their index that you can find with the command \"php bin/seed.php seed:list\".\n");
    CLI::default("\t-> Separate the indexes with a \",\" !! DO NOT USE ANY SPACES !! \n");
    CLI::default("\t-> Example : php bin/seed.php seed:[1,2] will use the second and third file if they exist.\n");
    CLI::default("\t-> Example : php bin/seed.php seed:[0] will only use the first file.\n");

    CLI::default("\n--------------------------\n");

    CLI::info("\nphp bin/migration.php status\n");
    CLI::default("\t-> Will check the status of the migrations, what is done or not.\n");
}
