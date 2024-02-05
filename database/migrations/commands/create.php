<?php

use bin\CLI;
require(__DIR__ . "/../../../bin/CLI.php");
define("NORMAL_TEMPLATE", -1);
define("CREATE_TEMPLATE", 0);
define("UPDATE_TEMPLATE", 2);
$date = date("YmdHis");
$migrationName = $argv[1] ?? null;


if ($migrationName == null) {
    throw new Exception("No migration name given");
}

$migrationName = trim($migrationName);

$matches = [];

//regarde si un nom de table a été passé
$match = preg_match("/.*(table_)(.+)/", $migrationName, $matches);


$migrationFile = "Migration_" . $date . "_" . $migrationName;

try {
    $fstream = fopen(__DIR__ . "/../" . $migrationFile . ".php", "w");

    switch(getTemplate($migrationName)){
        case CREATE_TEMPLATE:
            $fileContent = file_get_contents(__DIR__ . "/../template/createTemplate.txt");
            break;
        case UPDATE_TEMPLATE:
            $fileContent = file_get_contents(__DIR__ . "/../template/updateTemplate.txt");
            break;
        default:
            $fileContent = file_get_contents(__DIR__ . "/../template/normalTemplate.txt");
        break;
    }

    $fileContent = str_replace(":PLACE_HOLDER_CLASS", $migrationFile, $fileContent);

    if($match != 0){
        //un nom de table a été passé je peux le remplacer automatiquement $matches[2] étant le nom de la table donnée
        $fileContent = str_replace(":PLACE_HOLDER_TABLE", $matches[2], $fileContent);
    } else {
        //sinon nom par défaut
        $fileContent = str_replace(":PLACE_HOLDER_TABLE", "my_table", $fileContent);
    }
    
    fwrite($fstream, $fileContent);
} finally {
    fclose($fstream);
}
CLI::clear();
CLI::info("Successfully created your migration file as : ");
CLI::warning("database/migrations/" . $migrationFile . ".php", CLI::UNDERLINE);

function getTemplate(string $migrationName){
    if(substr($migrationName, 0, 6) == "create"){
        return CREATE_TEMPLATE;
    }

    if(substr($migrationName, 0, 6) == "update"){
        return UPDATE_TEMPLATE;
    }

    return NORMAL_TEMPLATE;
}
