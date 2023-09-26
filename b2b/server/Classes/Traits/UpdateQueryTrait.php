<?php
namespace Panel\Server\Classes\Traits;

trait UpdateQueryTrait
{
    public function update(array $data)
    {
        $setClauses = [];

        foreach ($data as $column => $value) {
            $setClauses[] = "$column = ?";
            $this->bindings[] = $value;
        }

        $query = "UPDATE {$this->table} SET " . implode(', ', $setClauses);

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->buildWhere();
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($this->bindings);

        return $statement->rowCount();
    }

}
