<?php
require(__DIR__ . "/../../../bin/CLI.php");
require(__DIR__ . "/../../../vendor/autoload.php");

use Database\Connection;
use Database\Parent\Database;

$seedName = $argv[1] ?? null;
$tableName = null;

if ($seedName == null) {
    throw new Exception("No seed name specified");
}

if (str_starts_with($seedName, "table_")) {
    $tableName = substr($seedName, 6);
}

$date = date("YmdHis");

$seedFile = "Seed" . "_" . $date . "_" . $seedName;

try {
    $fstream = fopen(__DIR__ . "/../" . $seedFile . ".php", "w");
    $fileContent = file_get_contents(__DIR__ . "/../template/createTemplate.txt");
    $columns = [];

    if (!is_null($tableName)) {
        $db = new Database(
            Connection::DETAILS["default"][Connection::HOST],
            Connection::DETAILS["default"][Connection::DATABASE_NAME],
            Connection::DETAILS["default"][Connection::PORT],
            Connection::DETAILS["default"][Connection::USERNAME],
            Connection::DETAILS["default"][Connection::PASSWORD]
        );

        $columns = $db->run(
            "
            SELECT column_name
            FROM information_schema.columns 
            WHERE table_schema = 'public' 
            AND table_name = '". $tableName ."';"
        )->fetchAll();
    }

    if (is_null($tableName) || $columns == []) {
        $fileContent = str_replace(
            ":PLACE_HOLDER_COLUMNS",
            '[
                "column_1" => "value",
                "column_2" => "value",
                "column_3" => "value",
            ],
            [
                "column_1" => "other_value",
                "column_2" => "other_value",
                "column_3" => "other_value",
            ], 
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : a table name wasn\'t given or the table doesn\'t exist yet, so you get this placeholder for the columns instead.',
            $fileContent
        );
    } else {
        $columns = array_map(fn($column) => "\t\t\t\t\"" . $column->column_name . "\" => \"my_value\"", $columns);
        $fileContent = str_replace(
            ":PLACE_HOLDER_COLUMNS",
            "[\n".
                implode(",\n", $columns)."
            ],
            [\n".
                implode(",\n", $columns)."
            ], 
            //add as many as you like (while trying to follow the same pattern if possible)
            //INFO : here all the columns in the table given, beware some columns may have a default value and be sure to respect the type of the columns.",
            $fileContent
        );
    }

    $fileContent = str_replace(":PLACE_HOLDER_CLASS", $seedFile, $fileContent);
    $fileContent = str_replace(":PLACE_HOLDER_TABLE", $tableName ?? "my_table", $fileContent);
    fwrite($fstream, $fileContent);
    CLI::clear();
    CLI::info("Successfully created your seeding file as : ");
    CLI::warning("database/seeds/" . $seedFile . ".php", TextStyle::UNDERLINE);
} finally {
    fclose($fstream);
}
