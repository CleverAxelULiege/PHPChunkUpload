<?php

namespace Migrations\Utilities;

use Database\Parent\Database;
use Migrations\Utilities\Schema;

class MigrationOperation
{
    public array $MIGRATIONS_DONE = [];
    public array $MIGRATIONS_FILES = [];
    public string $currentMigrationFile = "";

    public function __construct(public Database $db)
    {
        $this->createMigrationTableIfNotExists();
        $this->setMigrationsDone();
        $this->setMigrationsFiles();
        $this->requireAllMigrations();
    }

    public function status()
    {
        foreach ($this->MIGRATIONS_FILES as $migrationFile) {
            $migrationFileWithoutExtension =  str_replace(".php", "", $migrationFile);
            $creationDate = (int)substr($migrationFileWithoutExtension, 10, 14);

            if (in_array($creationDate, $this->MIGRATIONS_DONE)) {
                $this->colorLog($migrationFile . " DONE ✓", "s");
            } else {
                $this->colorLog($migrationFile . " UNTRACKED X", "w");
            }
        }
    }

    public function doStep(int $step)
    {
        $this->clearLog();
        if ($step == 0) {
            $this->colorLog("The step given can not be at 0, YOU FOOL (╯°□°）╯︵ ┻━┻", "w");
            return;
        }

        if ($step < 0) {
            $step = -$step;
            if ($this->MIGRATIONS_DONE == []) {
                $this->colorLog("----------Nothing to rollback ¯\\_(ツ)_/¯----------", "i");
                return;
            }
            $this->rollback($step);
        } else {
            $this->migrate($step);
        }
    }

    private function rollback(int $step)
    {

        $this->clearLog();
        if ($step > count($this->MIGRATIONS_DONE)) {
            $step = count($this->MIGRATIONS_DONE);
        }

        sort($this->MIGRATIONS_DONE);
        $this->MIGRATIONS_DONE = array_reverse($this->MIGRATIONS_DONE);
        for ($i = 0; $i < $step; $i++) {
            $this->currentMigrationFile = $this->retrieveFilenameFromDate($this->MIGRATIONS_DONE[$i]);
            $className = $this->retrieveClassnameFromFile($this->retrieveFilenameFromDate($this->MIGRATIONS_DONE[$i]));
            /**
             * @var \Migrations\Utilities\Migration
             */
            $migration = new $className();
            $schema = new Schema();
            $migration->down($schema);
            $queries = $migration->createInstructions($schema)->getQueries();
            foreach ($queries as $query) {
                $this->db->run($query);
            }
            $this->MIGRATIONS_DONE = array_filter($this->MIGRATIONS_DONE, fn ($m) => $m != $this->MIGRATIONS_DONE[$i]);
        }
        $this->updateHistoric();
        $this->colorLog("----------Successfully rollbacked " . $step . " migration(s) \\^o^/ ----------", "s");
    }

    public function migrate(?int $step)
    {

        $newMigrationsCount = count($this->MIGRATIONS_FILES) - count($this->MIGRATIONS_DONE);

        if ($newMigrationsCount == 0) {
            $this->clearLog();
            $this->colorLog("----------No new migration added ¯\\_(ツ)_/¯----------", "i");
            return;
        }

        $i = 0;

        foreach ($this->MIGRATIONS_FILES as $migrationFile) {

            if ($step != null && $i >= $step) {
                break;
            }
            $this->currentMigrationFile = $migrationFile;
            $migrationFileWithoutExtension =  str_replace(".php", "", $migrationFile);
            $className = $this->retrieveClassnameFromFile($migrationFile);
            $creationDate = (int)substr($migrationFileWithoutExtension, 10, 14);

            if (!in_array($creationDate, $this->MIGRATIONS_DONE)) {
                /**
                 * @var \Migrations\Utilities\Migration
                 */
                $migration = new $className();
                $schema = new Schema();
                $migration->up($schema);
                $queries = $migration->createInstructions($schema)->getQueries();

                foreach ($queries as $query) {
                    $this->db->run($query);
                }
                array_push($this->MIGRATIONS_DONE, $creationDate);

                $i++;
            }
        }

        $this->updateHistoric();
        if ($step === null) {
            $this->clearLog();
            $this->colorLog("----------Migration successfull \\^o^/ ----------", "s");
            return;
        }

        $this->colorLog("----------Successfully advanced " . $step . " migration(s) \\^o^/ ----------", "s");
    }



    private function retrieveClassnameFromFile(string $file)
    {
        $migrationFileWithoutExtension =  str_replace(".php", "", $file);
        return "\\Migrations\\" . $migrationFileWithoutExtension;
    }

    private function retrieveFilenameFromDate(string $date)
    {
        foreach ($this->MIGRATIONS_FILES as $file) {
            if (str_contains($file, $date)) {
                return $file;
            }
        }

        return "";
    }

    public function rollbackAll()
    {

        $this->clearLog();
        if ($this->MIGRATIONS_DONE == []) {
            $this->colorLog("----------Nothing to rollback ¯\\_(ツ)_/¯----------", "i");
            return;
        }

        sort($this->MIGRATIONS_DONE);
        $this->MIGRATIONS_DONE = array_reverse($this->MIGRATIONS_DONE);

        foreach ($this->MIGRATIONS_DONE as $migrationDone) {
            $this->currentMigrationFile = $this->retrieveFilenameFromDate($migrationDone);
            $className = $this->retrieveClassnameFromFile($this->retrieveFilenameFromDate($migrationDone));
            /**
             * @var \Migrations\Utilities\Migration
             */
            $migration = new $className();
            $schema = new Schema();
            $migration->down($schema);
            $queries = $migration->createInstructions($schema)->getQueries();
            foreach ($queries as $query) {
                $this->db->run($query);
            }
            array_pop($this->MIGRATIONS_DONE);
        }

        $this->updateHistoric();
        $this->colorLog("----------Reset successfull \\^o^/ ----------", "s");
    }

    public function updateHistoric()
    {
        $values = array_map(fn($m) => "(". $m .")", $this->MIGRATIONS_DONE);
        $this->db->run("TRUNCATE _migrations;");

        if($values !== []){
            $this->db->run("INSERT INTO _migrations(creation_timestamp) VALUES ". implode(",", $values) .";");
        }
    }

    private function setMigrationsDone()
    {
        $creationTimestamps = $this->db->run("SELECT creation_timestamp FROM _migrations ORDER BY creation_timestamp")->fetchAll();
        $this->MIGRATIONS_DONE = array_map(fn($ct) => $ct->creation_timestamp, $creationTimestamps);
    }

    private function requireAllMigrations()
    {
        foreach ($this->MIGRATIONS_FILES as $file) {
            require(__DIR__ . "/../" . $file);
        }
    }

    private function setMigrationsFiles()
    {
        $this->MIGRATIONS_FILES = array_filter(scandir(__DIR__ . "/../"), fn ($f) => is_file(__DIR__ . "/../" . $f));
    }

    private function createMigrationTableIfNotExists()
    {
        $this->db->run("CREATE TABLE IF NOT EXISTS _migrations (creation_timestamp BIGINT PRIMARY KEY)");
    }

    public function colorLog($str, $type = 'i')
    {
        switch ($type) {
            case 'e': //error
                echo "\033[31m$str \033[0m\n";
                break;
            case 's': //success
                echo "\033[32m$str \033[0m\n";
                break;
            case 'w': //warning
                echo "\033[33m$str \033[0m\n";
                break;
            case 'i': //info
                echo "\033[36m$str \033[0m\n";
                break;
        }
    }

    public function clearLog()
    {
        for ($i = 0; $i < 25; $i++) {
            echo "\n\r";
        }
    }
}
