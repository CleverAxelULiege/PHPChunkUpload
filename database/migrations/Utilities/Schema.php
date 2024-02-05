<?php

namespace Migrations\Utilities;

class Schema
{
    /**
     * @var \Migrations\Utilities\Table[]
     */
    private array $tables = [];

    public function createTable(string $name, callable $callback)
    {
        $table = new Table($name, Table::ADD);
        array_push($this->tables, $table);
        $callback($table);
    }

    public function updateTable(string $name, callable $callback)
    {
        $table = new Table($name, Table::UPDATE);
        array_push($this->tables, $table);
        $callback($table);
    }

    public function dropTable(string $name){
        $table = new Table($name, Table::DELETE);
        array_push($this->tables, $table);
    }

    public function renameTable(string $name, string $newName){
        $table = new Table($name, Table::UPDATE);
        $table->newName = $newName;
        array_push($this->tables, $table);
    }

    public function getTables()
    {
        return $this->tables;
    }
}
