<?php

namespace Core;

use DateTime;
use ReflectionProperty;

class Model
{
    public ?int $id;

    protected static function get_table_name(): string
    {
        $class_name = str_replace('\\', '/', get_called_class());
        return strtolower(basename($class_name)) . 's';
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
        $class = get_called_class();
        $model = new $class();
        $data = $stmt->fetch();
        foreach ($data as $property => $value) {
            if (property_exists($model, $property)) {
                $rp = new ReflectionProperty($model, $property);
                if ($rp->getType()->__tostring() === 'DateTime') {
                    $model->$property = new DateTime($value);
                } else {
                    $model->$property = $value;
                }
            }
        }
        return $model;
    }
}
