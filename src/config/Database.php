<?php

namespace Charmaine\StudentGradeApi\Config;

use PDO;
use PDOException;

class Database {
    private string $host = "localhost";
    private string $databaseName = "student_management";
    private string $username = "root";
    private string $password = "";
    private ?PDO $connection = null;

    public function __construct() {
        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->databaseName}", $this->username, $this->password);
            
            $this->connection->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
        } catch (PDOException $e) {
            die("Connection failed: " . $e->getMessage());
        }
    }    

    private function connect(): void {
        try {
            $this->connection = new PDO(
                "mysql:host={$this->host};dbname={$this->databaseName}",
                $this->username,
                $this->password
            );
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $exception) {
            throw new PDOException("Database connection failed: " . $exception->getMessage());
        }
    }

    /**
     * @return PDO|null
     */
    public function getConnection(): ?PDO {
        return $this->connection;
    }
}