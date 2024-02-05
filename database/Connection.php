<?php

namespace Database;

class Connection{
    const HOST = "host";
    const DATABASE_NAME = "db_name";
    const PORT = "port";
    const USERNAME = "username";
    const PASSWORD = "password";
    
    const DETAILS = [
        "default" => [
            Connection::HOST => "localhost",
            Connection::DATABASE_NAME => "surveys",
            Connection::PORT => 5432,
            Connection::USERNAME => "postgres",
            Connection::PASSWORD => "admin"
        ],
    ];
}