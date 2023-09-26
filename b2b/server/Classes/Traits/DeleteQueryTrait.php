<?php


namespace Panel\Server\Classes\Traits;

trait DeleteQueryTrait
{
    public function delete()
    {
        $query = "DELETE FROM {$this->table}";

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->buildWhere();
        }
        $statement = $this->connection->prepare($query);
        $statement->execute($this->bindings);

        return $statement->rowCount();
    }
}
