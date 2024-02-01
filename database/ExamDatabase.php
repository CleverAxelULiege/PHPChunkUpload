<?php

namespace Database;

use Database\Parent\PostgresDatabase;


class ExamDatabase extends PostgresDatabase
{
    private string $dbName = "exam";
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
