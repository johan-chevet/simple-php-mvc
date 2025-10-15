<?php

namespace Core;

use DateTime;

class Model
{
    protected static string $table_name;
    public ?int $id;

    protected static function get_table_name(): string
    {
        if (empty(static::$table_name)) {
            $class_name = str_replace('\\', '/', get_called_class());
            static::$table_name = strtolower(basename($class_name)) . 's';
        }
        return static::$table_name;
    }

    public function save(): void
    {
        // Get all properties from child class
        $data = get_object_vars($this);

        // transform Datetime object to string
        foreach ($data as &$value) {
            if ($value instanceof DateTime) {
                $value = $value->format("Y-m-d H:i:s");
                var_dump($value);
            }
        }

        // Filter class properties that are objects
        $data = array_filter($data, fn($val) => !is_object($val));

        // Update if id is set or Insert
        if (isset($this->id)) {
            $set = array_map(
                fn($d) => "$d = ?",
                array_keys($data)
            );
            $set = implode(', ', $set);
            $sql = "UPDATE " . static::get_table_name() . " SET $set WHERE id = ?";
            $stmt = Database::pdo()->prepare($sql);
            $stmt->execute([...array_values($data), $this->id]);
        } else {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_fill(0, count($data), '?'));
            $sql = "INSERT INTO " . static::get_table_name() . "($columns) VALUES ($placeholders)";
            $stmt = Database::pdo()->prepare($sql);
            $stmt->execute(array_values($data));
            $this->id = (int) Database::pdo()->lastInsertId();
        }
    }

    public function delete(): bool
    {
        if (!isset($this->id)) {
            return false;
        }
        return Database::pdo()
            ->prepare("DELETE FROM " . static::get_table_name() . " WHERE id = ?")
            ->execute([$this->id]);
    }

    public static function find_by_id(int $id): static|false
    {
        $sql = "SELECT * FROM " . static::get_table_name() . " WHERE id = ?";
        $stmt = Database::pdo()->prepare($sql);
        $stmt->execute([$id]);
        $stmt->setFetchMode(\PDO::FETCH_CLASS, get_called_class());
        return $stmt->fetch();
    }
}
