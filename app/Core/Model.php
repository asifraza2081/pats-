<?php

declare(strict_types=1);

namespace App\Core;

use App\Core\Database;

abstract class Model
{
    protected static string $table = '';
    protected static string $primaryKey = 'id';

    protected Database $db;

    public function __construct()
    {
        $this->db = Database::getInstance();
    }

    /**
     * Find a single record by primary key.
     */
    public static function find(int $id): ?array
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            'SELECT * FROM ' . static::$table . ' WHERE ' . static::$primaryKey . ' = ?',
            [$id]
        );
    }

    /**
     * Find a single record by arbitrary column.
     */
    public static function findBy(string $column, mixed $value): ?array
    {
        $db = Database::getInstance();
        return $db->fetchOne(
            'SELECT * FROM ' . static::$table . ' WHERE ' . $column . ' = ? LIMIT 1',
            [$value]
        );
    }

    /**
     * Get all records from the table.
     */
    public static function all(string $orderBy = 'id DESC'): array
    {
        $db = Database::getInstance();
        return $db->fetchAll('SELECT * FROM ' . static::$table . ' ORDER BY ' . $orderBy);
    }

    /**
     * Get records matching conditions.
     */
    public static function where(array $conditions, string $orderBy = 'id DESC'): array
    {
        $db     = Database::getInstance();
        $wheres = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($conditions)));
        return $db->fetchAll(
            'SELECT * FROM ' . static::$table . ' WHERE ' . $wheres . ' ORDER BY ' . $orderBy,
            array_values($conditions)
        );
    }

    /**
     * Create a new record. Returns inserted ID.
     */
    public static function create(array $data): int
    {
        return Database::getInstance()->insert(static::$table, $data);
    }

    /**
     * Update records matching $where. Returns affected rows.
     */
    public static function updateWhere(array $data, array $where): int
    {
        return Database::getInstance()->update(static::$table, $data, $where);
    }

    /**
     * Delete records matching $where.
     */
    public static function deleteWhere(array $where): int
    {
        return Database::getInstance()->delete(static::$table, $where);
    }

    /**
     * Count records matching conditions.
     */
    public static function count(array $conditions = []): int
    {
        $db  = Database::getInstance();
        $sql = 'SELECT COUNT(*) as cnt FROM ' . static::$table;
        if ($conditions) {
            $wheres = implode(' AND ', array_map(fn($k) => "{$k} = ?", array_keys($conditions)));
            $sql   .= ' WHERE ' . $wheres;
        }
        return (int) ($db->fetchOne($sql, array_values($conditions))['cnt'] ?? 0);
    }

    /**
     * Check if a record exists.
     */
    public static function exists(array $conditions): bool
    {
        return static::count($conditions) > 0;
    }

    /**
     * Run a raw query via the DB instance.
     */
    protected function query(string $sql, array $params = []): array
    {
        return $this->db->fetchAll($sql, $params);
    }
}
