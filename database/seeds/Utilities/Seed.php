<?php

namespace Seeds\Utilities;

class Seed implements SeedInterface{
    public string $table;

    public array $tablesWithData = [];

    public function table(string $table){
        $this->table = $table;

        if(!isset($this->tablesWithData[$table])){
            $this->tablesWithData[$table] = [];
        }

        return $this;
    }

    /**
     * @param array{columnName: string, value: string} $data
     */
    public function with(array $data){
        array_push($this->tablesWithData[$this->table], ...$data);
    }
}