<?php

namespace Core;

class Database
{
    private static ?\PDO $_pdo;

    public static function pdo(): \PDO
    {
        if (!isset(self::$_pdo)) {
            try {
                $dsn = "mysql:host=" . \DB_HOST . ";dbname=" . \DB_NAME;
                $options = [
                    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
                    \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                    \PDO::ATTR_EMULATE_PREPARES => false,
                ];
                self::$_pdo = new \PDO($dsn, \DB_USER, \DB_PASS, $options);
            } catch (\PDOException $e) {
                die("Erreur de connexion à la base de données : " . $e->getMessage());
            }
        }
        return self::$_pdo;
    }
}
