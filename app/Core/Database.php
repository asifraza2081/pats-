<?php

declare(strict_types=1);

namespace App\Core;

use PDO;
use PDOException;

class Database
{
    private static ?Database $instance = null;
    private PDO $pdo;

    private function __construct()
    {
        $config = require BASE_PATH . '/config/database.php';

        $dsn = "mysql:host={$config['host']};dbname={$config['name']};charset={$config['charset']}";

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ];

        try {
            $this->pdo = new PDO($dsn, $config['user'], $config['pass'], $options);
        } catch (PDOException $e) {
            error_log('Database connection failed: ' . $e->getMessage());
            throw new \RuntimeException('Database connection failed. Please try again later.');
        }
    }

    public static function getInstance(): static
    {
        if (static::$instance === null) {
            static::$instance = new static();
        }
        return static::$instance;
    }

    public function getPdo(): PDO
    {
        return $this->pdo;
    }

    /**
     * Run a query and return the PDOStatement.
     */
    public function query(string $sql, array $params = []): \PDOStatement
    {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch a single row.
     */
    public function fetchOne(string $sql, array $params = []): ?array
    {
        $result = $this->query($sql, $params)->fetch();
        return $result ?: null;
    }

    /**
     * Fetch all rows.
     */
    public function fetchAll(string $sql, array $params = []): array
    {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Insert a row and return the last insert ID.
     */
    public function insert(string $table, array $data): int
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));
        $sql = "INSERT INTO {$table} ({$columns}) VALUES ({$placeholders})";
        $this->query($sql, array_values($data));
        return (int) $this->pdo->lastInsertId();
    }

    /**
     * Update rows matching $where and return number of affected rows.
     */
    public function update(string $table, array $data, array $where): int
    {
        $set   = implode(', ', array_map(fn($k) => "{$k} = ?", array_keys($data)));
        $cond  = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($where)));
        $sql   = "UPDATE {$table} SET {$set} WHERE {$cond}";
        $stmt  = $this->query($sql, [...array_values($data), ...array_values($where)]);
        return $stmt->rowCount();
    }

    /**
     * Delete rows and return number of affected rows.
     */
    public function delete(string $table, array $where): int
    {
        $cond = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($where)));
        $sql  = "DELETE FROM {$table} WHERE {$cond}";
        return $this->query($sql, array_values($where))->rowCount();
    }

    public function beginTransaction(): void  { $this->pdo->beginTransaction(); }
    public function commit(): void            { $this->pdo->commit(); }
    public function rollBack(): void          { $this->pdo->rollBack(); }
}
