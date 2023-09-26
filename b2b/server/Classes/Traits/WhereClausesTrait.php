<?php


namespace Panel\Server\Classes\Traits;

trait WhereClausesTrait
{
    public function whereBetween($column, $from, $to, $boolean = 'AND')
    {
        $operator = 'BETWEEN';
        $this->where[] = compact('column', 'from', 'to', 'boolean', 'operator');
        $this->bindings[] = $from;
        $this->bindings[] = $to;
        return $this;
    }

    public function where($column, $operator, $value = null, $boolean = 'AND')
    {
        // if (func_num_args() === 3) {
        //     $value = $operator;
        //     $operator = '=';
        // }

        $this->where[] = compact('column', 'operator', 'value', 'boolean');
        $this->bindings[] = $value;
        $this->boolean = $boolean;
        return $this;
    }

    public function orWhere($column, $operator, $value = null)
    {
        return $this->where($column, $operator, $value, 'OR');
    }

    public function andWhere($column, $operator, $value = null)
    {
        return $this->where($column, $operator, $value, 'AND');
    }

    public function search($column, $value, $boolean = 'AND'): self
    {
        // For a basic search, you can use the LIKE operator
        $operator = 'LIKE';
        $value = '%' . $value . '%';

        $this->where[] = compact('column', 'operator', 'value', 'boolean');
        $this->bindings[] = $value;
        $this->boolean = $boolean;
        return $this;
    }

    public function whereNull($column, $boolean = 'AND')
    {

        $operator = 'IS';
        $value = '';

        $this->where[] = compact('column', 'operator', 'value', 'boolean');
        $this->bindings[] = $value;
        $this->boolean = $boolean;
        return $this;
    }
}
