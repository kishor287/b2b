<?php

namespace Panel\Server\Classes\Config;

require_once(ROOT_PATH.'/vendor/autoload.php');

use Closure;
use PDO;
use Panel\Server\Classes\Controller\BaseController;
use Panel\Server\Classes\Traits\DeleteQueryTrait;
use Panel\Server\Classes\Traits\InsertQueryTrait;
use Panel\Server\Classes\Traits\ResultComponentsTrait;
use Panel\Server\Classes\Traits\UpdateQueryTrait;
use Panel\Server\Classes\Traits\WhereClausesTrait;

class Builder extends BaseController
{
    use InsertQueryTrait, DeleteQueryTrait, ResultComponentsTrait, UpdateQueryTrait, WhereClausesTrait;
    private $table;
    private $select = '*';
    private $where = [];
    private $bindings = [];
    private $joins = [];
    private $groupBy = [];
    protected $orderBy = '';
    protected $limit;
    protected $offset;

    protected int $page;
    protected int $perPage;

    public bool $debug;

    public function __construct()
    {
        parent::__construct();
    }

    public function table(string $table)
    {
        $this->table = $table;
        return $this;
    }

    public function select($columns)
    {
        $this->select = is_array($columns) ? implode(', ', $columns) : $columns;
        return $this;
    }

    public function join(string $table, string $firstColumn, string $operator, string $secondColumn, string $type = 'INNER')
    {
        $this->joins[] = "$type JOIN $table ON $firstColumn $operator $secondColumn";
        return $this;
    }
    public function groupBy($columns)
    {
        $this->groupBy = is_array($columns) ? $columns : [$columns];
        return $this;
    }
    public function take($limit)
    {
        $this->limit = $limit;
        return $this;
    }
    public function get($debug = false)
    {
        if (!empty($this->page)) {
            $page = !empty($this->page) ? $this->page : 1;
            $perPage = !empty($this->perPage) ? $this->perPage : 10;
            $this->offset = ($page - 1) * $perPage;
        }

        $query = "SELECT {$this->select} FROM {$this->table}";

        if (!empty($this->joins)) {
            $query .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->buildWhere();
        }

        if (!empty($this->groupBy)) {
            $query .= " GROUP BY " . implode(', ', $this->groupBy);
        }
        if ($this->orderBy !== null && !empty($this->orderBy)) {
            $query .= " ORDER BY " . $this->orderBy;
        }

        if ($this->limit !== null) {
            $query .= " LIMIT " . $this->limit;
        }

        if ($this->offset !== null) {
            $query .= " OFFSET " . $this->offset;
        }
        $statement = $this->connection->prepare($query);
        if($debug){
            print_r($this->bindings);
            echo '<br>';
            dd($statement);
        }
        $statement->execute($this->bindings);
        $res = $statement->fetchAll(PDO::FETCH_ASSOC);
        return $res;
    }
    public function count()
    {
        $query = "SELECT COUNT(*) as total FROM {$this->table}";

        if (!empty($this->joins)) {
            $query .= " " . implode(' ', $this->joins);
        }

        if (!empty($this->where)) {
            $query .= " WHERE " . $this->buildWhere();
        }

        if (!empty($this->groupBy)) {
            $query .= " GROUP BY " . implode(', ', $this->groupBy);
        }

        $statement = $this->connection->prepare($query);
        $statement->execute($this->bindings);

        $result = $statement->fetch(PDO::FETCH_ASSOC);
        return isset($result['total']) ? (int) $result['total'] : 0;
    }

    public function orderBy(string $column, string $direction = 'ASC')
    {
        // Validate the sorting direction to ensure it is either 'ASC' or 'DESC'
        $direction = strtoupper($direction);
        if ($direction !== 'ASC' && $direction !== 'DESC') {
            throw new \InvalidArgumentException("Invalid sorting direction. Only 'ASC' or 'DESC' are allowed.");
        }

        $this->orderBy = "$column $direction";
        return $this;
    }

    private function buildWhere()
    {
        $whereClauses = [];

        foreach ($this->where as $condition) {
            $column = $condition['column'];
            $operator = $condition['operator'];
            $value = $condition['value'];
            $boolean = $condition['boolean'];

            if ($value instanceof Closure) {
                $builder = new self();
                $value($builder);
                $value = '(' . $builder->buildWhere() . ')';
            } else {
                $value = '?';
            }

            $whereClauses[] = "$column $operator $value $boolean";
        }
        // print_r($whereClauses);
        // die;
        return implode(' ', $whereClauses);
    }

    private function buildBindings($values)
    {
        return implode(', ', array_fill(0, count($values), '?'));
    }


}