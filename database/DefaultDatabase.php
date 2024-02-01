<?php

namespace Database;

use Database\Parent\PostgresDatabase;


class DefaultDatabase extends PostgresDatabase
{
    private string $dbName = "default";
    public function __construct()
    {
        parent::__construct(
            Connection::DETAILS[$this->dbName][Connection::HOST],
            Connection::DETAILS[$this->dbName][Connection::DATABASE_NAME],
            Connection::DETAILS[$this->dbName][Connection::PORT],
            Connection::DETAILS[$this->dbName][Connection::USERNAME],
            Connection::DETAILS[$this->dbName][Connection::PASSWORD]
        );
    }
}
