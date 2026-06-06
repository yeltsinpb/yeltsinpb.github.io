<?php
declare(strict_types=1);

namespace App;

use PDO;

final class Database
{
    private static ?PDO $pdo = null;

    public static function init(string $path): void
    {
        $dir = dirname($path);
        if (!is_dir($dir)) mkdir($dir, 0775, true);

        self::$pdo = new PDO('sqlite:' . $path);
        self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
        self::$pdo->exec('PRAGMA foreign_keys = ON');
    }

    public static function pdo(): PDO
    {
        if (!self::$pdo) throw new \RuntimeException('Database not initialized');
        return self::$pdo;
    }

    public static function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = self::pdo()->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    public static function fetchAll(string $sql, array $params = []): array
    {
        return self::query($sql, $params)->fetchAll();
    }

    public static function fetchOne(string $sql, array $params = []): ?array
    {
        $row = self::query($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    public static function insert(string $table, array $data): string
    {
        $cols = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        self::query("INSERT INTO $table ($cols) VALUES ($placeholders)", array_values($data));
        return self::pdo()->lastInsertId();
    }

    public static function update(string $table, array $data, string $where, array $whereParams): void
    {
        $set = implode(',', array_map(fn($k) => "$k = ?", array_keys($data)));
        self::query(
            "UPDATE $table SET $set WHERE $where",
            array_merge(array_values($data), $whereParams)
        );
    }

    public static function delete(string $table, string $where, array $params): void
    {
        self::query("DELETE FROM $table WHERE $where", $params);
    }
}
