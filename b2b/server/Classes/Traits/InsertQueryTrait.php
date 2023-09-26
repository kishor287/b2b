<?php

namespace Panel\Server\Classes\Traits;

trait InsertQueryTrait
{
    public function insert(array $data)
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_fill(0, count($data), '?'));

        $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
        $statement = $this->connection->prepare($query);
        $statement->execute(array_values($data));

        return $this->connection->lastInsertId();
    }
}
