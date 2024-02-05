<?php

namespace Database\Parent;

class PostgresDatabase extends Database{
    public function __construct(string $host, string $dbName, string $port, string $user, string $password)
    {
        $this->driver = "pgsql";
        parent::__construct($host, $dbName, $port, $user, $password);
    }
}