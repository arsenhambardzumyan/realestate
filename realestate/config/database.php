<?php

class Database
{
    private static $connection;

    public static function getConnection()
    {
        if (!self::$connection) {
            $config = require __DIR__ . '/db.php';
            self::$connection = new PDO(
                "mysql:host={$config['host']};dbname={$config['dbname']}",
                $config['user'],
                $config['password']
            );
            self::$connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return self::$connection;
    }
}
