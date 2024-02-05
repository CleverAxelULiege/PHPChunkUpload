<?php

use Database\Connection;
use Database\Parent\Database;
use Seeds\Utilities\SeedOperation;

require(__DIR__ . "/../../../vendor/autoload.php");

$action = $argv[1] ?? "";

$db = new Database(
    Connection::DETAILS["default"][Connection::HOST],
    Connection::DETAILS["default"][Connection::DATABASE_NAME],
    Connection::DETAILS["default"][Connection::PORT],
    Connection::DETAILS["default"][Connection::USERNAME],
    Connection::DETAILS["default"][Connection::PASSWORD]
);
$db->beginTransaction();

$seedOperation = new SeedOperation($db);
$seedOperation->setAllSeedsFile();
try {
    if (str_starts_with($action, "--only:")) {
        $seedOperation->seedSelectedFiles(substr($action, 7));
    } else if (str_starts_with($action, "--truncate:")) {
        $truncateAction = substr($action, 11);
        switch($truncateAction){
            case"all":
                $seedOperation->truncateAll();
                break;
            default:
                $seedOperation->truncateTable($truncateAction);
                break;
        }
    } else {
        switch ($action) {
            case "--all":
                $seedOperation->seedAllFiles();
                break;
            case "--list":
                $seedOperation->listAllSeedsFile();
                break;
        }
    }
    $db->commitTransaction();
} catch (Exception $e) {
    echo "FAILED TO SEED THE DATABASE ROLLING BACK THE SEEDING\n";
    $db->rollbackTransaction();
}
