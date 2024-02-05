<?php

namespace Database\Parent;

use PDO;
use PDOException;

class Database
{
    private PDO $pdo;
    protected string $driver = "pgsql"; //default driver
    public function __construct(string $host, string $dbName, string $port, string $user, string $password)
    {
        try {
            $dsn = "$this->driver:host=$host;port=$port;dbname=$dbName;";
            $this->pdo = new PDO($dsn, $user, $password);
            $this->pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_OBJ);
            $this->pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            throw new PDOException($e->getMessage(), (int)$e->getCode(), $e);
        }
    }

    public function run(string $query, $args = null)
    {
        if (is_null($args)) {
            return $this->pdo->query($query);
        }
        $stmt = $this->pdo->prepare($query);
        $stmt->execute($args);
        return $stmt;
    }

    public function beginTransaction()
    {
        $this->pdo->beginTransaction();
    }

    public function commitTransaction()
    {
        $this->pdo->commit();
    }

    public function rollbackTransaction()
    {
        $this->pdo->rollBack();
    }
}
