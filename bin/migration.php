<?php

use bin\CLI;

require(__DIR__ . "/CLI.php");

$action = trim($argv[1]) ?? "";

if (str_starts_with($action, "make:")) { //souhaite créer une nouvelle migration
    $migrationName = substr($action, 5);
    echo passthru("php database/migrations/commands/create.php " . $migrationName);
} 
else if (str_starts_with($action, "migrate:")) { //souhaite migrer
    $migrateAction = trim(substr($action, 8));

    if (is_numeric($migrateAction)) {
        $stepsToMigrate = (int)$migrateAction;
        echo passthru("php database/migrations/commands/migrate.php --do:" . $stepsToMigrate);
    } else {
        switch ($migrateAction) {
            case "reset":
                echo passthru("php database/migrations/commands/migrate.php --reset");
                break;
            case "all":
                echo passthru("php database/migrations/commands/migrate.php --all");
                break;
            default:
                CLI::clear();
                CLI::warning("Unknown command to migrate. Here are the possible commands : \n");
                CLI::warning("php bin/migration.php migrate:all");
                CLI::info(" -> Will migrate all remaining migrations\n");

                CLI::warning("php bin/migration.php migrate:reset");
                CLI::info(" -> Will rollback all migrations done\n");

                CLI::warning("php bin/migration.php migrate:<integer>");
                CLI::info(" -> Advance or rollback from the integer given\n");
                break;
        }
    }
} 
else if ($action == "status") { //souhaite voir le status des migrations
    CLI::clear(15);
    echo passthru("php database/migrations/commands/migrate.php --status");
} 
else if($action == "help") {
    CLI::clear(15);
    listCommands();
} 
else {
    CLI::clear(15);
    listCommands(); 
    CLI::warning("Unknown command. Check above to see the list of commands available.\n");
}

function listCommands(){
    CLI::warning("Here are the possible commands for the migrations :\n");
    CLI::info("php bin/migration.php make:<your_migration_name>\n");
    CLI::default("\t-> Example : php bin/migrations.php make:create_table_users will create a migrations for a table with the name users.\n");
    CLI::default("\t-> Example : php bin/migrations.php make:update_table_users will create a migrations to update a table with the name users.\n");

    CLI::default("\n--------------------------\n");

    CLI::info("\nphp bin/migration.php migrate:all\n");
    CLI::default("\t-> Will migrate all remaining migrations\n");

    CLI::info("\nphp bin/migration.php migrate:reset\n");
    CLI::default("\t-> Will rollback all migrations done\n");

    CLI::info("\nphp bin/migration.php migrate:<integer>\n");
    CLI::default("\t-> Will advance or rollback from the integer passed\n");
    CLI::default("\t-> Example : php bin/migrations.php migrate:2 will advance of 2 migrations.\n");
    CLI::default("\t-> Example : php bin/migrations.php migrate:-1 will rollback 1 migration.\n");

    CLI::default("\n--------------------------\n");

    CLI::info("\nphp bin/migration.php status\n");
    CLI::default("\t-> Will check the status of the migrations, what is done or not.\n");
}
