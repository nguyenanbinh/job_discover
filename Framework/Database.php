<?php
namespace Framework;

use PDO;
use PDOException;
class Database {
    public $connection;

    public function __construct($config)
    {
        try {
            $dsn = "pgsql:host={$config["host"]};port={$config["port"]};dbname={$config["dbname"]};";

            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_OBJ
            ];

            $this->connection = new PDO($dsn, $config["username"], $config["password"], $options);
        } catch (PDOException $e) {
            die($e->getMessage());
        }
    }

    /**
     * @param $query
     * @return false|PDOStatement
     * @throws Exception
     */
    public function query($query, $params = []) {
        try {
            $statement = $this->connection->prepare($query);

            foreach ($params as $param => $value) {
                $statement->bindValue(":" . $param, $value);
            }
            $statement->execute();
            return $statement;

        } catch (PDOException $e) {
            throw new PDOException("Query failed to execute : {$e->getMessage()}");
        }
    }

}