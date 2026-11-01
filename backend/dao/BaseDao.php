<?php

class BaseDao {
    private static $connection = null;

    protected function db() {
        if (self::$connection === null) {
            $configPath = __DIR__ . '/../config/config.php';
            if (!is_file($configPath)) {
                throw new RuntimeException('Database configuration is missing. Copy config.example.php to config.php.');
            }

            $config = require $configPath;
            $dsn = 'mysql:host=' . $config['db_host'] . ';dbname=' . $config['db_name'] . ';charset=utf8mb4';
            try {
                self::$connection = new PDO($dsn, $config['db_user'], $config['db_pass'], [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                ]);
            } catch (PDOException $error) {
                error_log($error->getMessage());
                throw new RuntimeException('Cannot connect to the database. Check the database name, user, and password in config.php.', 0, $error);
            }
        }

        return self::$connection;
    }

    protected function query($sql, $params = []) {
        $statement = $this->db()->prepare($sql);
        $statement->execute($params);
        return $statement;
    }
}
