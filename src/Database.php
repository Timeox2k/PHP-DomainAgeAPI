<?php

namespace TimeoxTwok\DomainAgeApi;

use Dotenv\Dotenv;
use PDO;
use PDOStatement;

class Database
{
    private static ?Database $instance = null;
    private PDO $mysql;

    private function __construct()
    {
        $dotenv = Dotenv::createImmutable($_SERVER['DOCUMENT_ROOT']);
        $dotenv->load();
        $database_host = $_ENV['MYSQL_DB_HOST'];
        $database_name = $_ENV['MYSQL_DB_NAME'];
        $database_user = $_ENV['MYSQL_DB_USER'];
        $database_password = $_ENV['MYSQL_DB_PASSWORD'];

        $mysql = new PDO("mysql:host=$database_host;port=3306;dbname=$database_name", $database_user, $database_password);
        $mysql->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $this->mysql = $mysql;
    }

    /**
     * @return Database
     */
    public static function getInstance(): Database
    {
        if (self::$instance === null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    /**
     * @param string $query
     * @return PDOStatement
     */
    public function prepare(string $query): PDOStatement
    {
        return $this->mysql->prepare($query);
    }
}