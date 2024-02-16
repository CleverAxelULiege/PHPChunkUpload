<?php

namespace Seeds\Utilities;

require(__DIR__ . "/../../../bin/CLI.php");

use bin\CLI;
use Database\Parent\Database;

class SeedOperation
{
    /** @var \Seeds\Utilities\Seed[] */
    private array $seeds = [];
    private array $seedFiles = [];

    public function __construct(private Database $db)
    {
    }

    public function truncateAll()
    {
        $tables = [];
        $tables = $this->db->run("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema' AND tablename != '_migrations';")->fetchAll();

        if ($tables == []) {
            CLI::clear();
            CLI::warning("No table found to truncate.\n");
            return;
        }

        foreach ($tables as $table) {
            $this->db->run("TRUNCATE TABLE " . $table->tablename . " RESTART IDENTITY CASCADE;");
        }

        CLI::clear();
        CLI::success("All tables have been truncated.\n");
    }

    public function truncateTable(string $table){
        $tables = [];
        $tables = $this->db->run("SELECT tablename FROM pg_catalog.pg_tables WHERE schemaname != 'pg_catalog' AND schemaname != 'information_schema' AND tablename != '_migrations' AND tablename = :tablename", [
            "tablename" => $table
        ])->fetchAll();

        if ($tables == []) {
            CLI::clear();
            CLI::warning("The table ". $table ." doesn't exist.\n");
            return;
        }

        $this->db->run("TRUNCATE TABLE " . $table . " CASCADE;");
        $this->db->run("TRUNCATE TABLE " . $table . " RESTART IDENTITY;");
        CLI::clear();
        CLI::success("The table ". $table ." has been truncated.\n");
    }

    public function setAllSeedsFile()
    {
        $this->seedFiles = array_values(array_filter(scandir(__DIR__ . "/../"), fn ($f) => is_file(__DIR__ . "/../" . $f)));
        $this->requireAllSeedFiles();
    }

    public function listAllSeedsFile()
    {
        CLI::clear();
        $seedFileLength = count($this->seedFiles);
        if ($seedFileLength == 0) {
            CLI::warning("No seeding file found.\n");
            return;
        }

        CLI::warning("Here's the list of all your seeding files with their index :\n");
        for ($i = 0; $i < $seedFileLength; $i++) {
            CLI::info("(" . $i . ")     database/seeds/" . $this->seedFiles[$i] . "\n", CLI::UNDERLINE);
        }
    }

    private function requireAllSeedFiles()
    {
        foreach ($this->seedFiles as $seedFile) {
            require(__DIR__ . "/../" . $seedFile);
        }
    }

    public function seedSelectedFiles(string $subArray)
    {
        $seedFileLength = count($this->seedFiles);
        $matches = [];
        if (preg_match("/(\[)((\s?\d*,*)*)(\])/", $subArray, $matches) != 0) { // si un "tableau" a été passé
            $filterPossibilyEmptyValuesAndNonExistentIndex = array_filter(
                explode(",", $matches[2]),
                fn ($index) => trim($index) != "" && $index < $seedFileLength
            );

            $indexesToUseToSeed = array_unique(
                array_map(
                    fn ($index) => (int)$index,
                    $filterPossibilyEmptyValuesAndNonExistentIndex
                )
            );
        }
        $this->createSeeds($indexesToUseToSeed);
        $this->insertSeeds();
    }

    public function seedAllFiles()
    {
        $this->createSeeds();
        $this->insertSeeds();
    }

    private function createSeeds(array $indexesToUseToSeed = null)
    {

        if ($indexesToUseToSeed !== null) {
            $seedFileLength = count($this->seedFiles);
            for ($i = 0; $i < $seedFileLength; $i++) {

                if (in_array($i, $indexesToUseToSeed)) {
                    $seed = new Seed();
                    array_push($this->seeds, $seed);
                    $className = $this->getClassNameFromFile($this->seedFiles[$i]);
                    /**
                     * @var \Seeds\Utilities\AbstractSeed
                     */
                    $abstractSeed = new $className();
                    $abstractSeed->seed($seed);
                }
            }
            CLI::clear();
            CLI::success("Successfully seeded the database with the selected indexes. (if those existed)\n");
            return;
        }

        foreach ($this->seedFiles as $file) {
            $seed = new Seed();
            array_push($this->seeds, $seed);
            $className = $this->getClassNameFromFile($file);
            /**
             * @var \Seeds\Utilities\AbstractSeed
             */
            $abstractSeed = new $className();
            $abstractSeed->seed($seed);
        }
        CLI::clear();
        CLI::success("Successfully seeded the database with all the seeding files.\n");
    }

    private function insertSeeds()
    {
        foreach ($this->seeds as $seed) {
            foreach ($seed->tablesWithData as $tableName => $table) {
                foreach ($table as $columns) {
                    $this->createInsert($tableName, $columns);
                }
            }
        }

    }

    private function createInsert(string $tableName, array $columns)
    {
        $placeHolders = array_map(fn () => "?", $columns);
        $query = "INSERT INTO " . $tableName . "(" . implode(", ", array_keys($columns)) . ") VALUES (" . implode(", ", $placeHolders) . ")";
        $this->db->run($query, array_values($columns));
    }

    private function getClassNameFromFile($file)
    {
        return "\\Seeds\\" . str_replace(".php", "", $file);
    }
}
