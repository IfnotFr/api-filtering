<?php

namespace Ifnot\ApiFiltering\Query;

/**
 * Class Builder
 * @package Ifnot\ApiFiltering\Query
 */
class Builder
{
    protected $where = [];

    protected $order_by = [];

    protected $limit = null;

    protected $offset = null;

    /**
     * Add a basic where clause to the filter.
     *
     * @param  string $column
     * @param  string $operator
     * @param  mixed $value
     * @return $this
     */
    public function where($column, $operator = null, $value = null)
    {
        // Here we will make some assumptions about the operator. If only 2 values are
        // passed to the method, we will assume that the operator is an equals sign
        // and keep going. Otherwise, we'll require the operator to be passed in.
        list($value, $operator) = $this->prepareValueAndOperator($value, $operator, func_num_args() == 2);

        $this->where[$column][$operator] = $value;

        return $this;
    }

    /**
     * Add a where between statement to the filter.
     *
     * @param  string $column
     * @param  array $values
     * @param  bool $not
     * @return $this
     */
    public function whereBetween($column, array $values, $not = false)
    {
        $this->where[$column]['>='] = $values[$not ? 1 : 0];
        $this->where[$column]['<='] = $values[$not ? 0 : 1];

        return $this;
    }

    /**
     * Add a where not between statement to the filter.
     *
     * @param  string $column
     * @param  array $values
     * @return $this
     */
    public function whereNotBetween($column, $values)
    {
        return $this->whereBetween($column, $values, true);
    }

    /**
     * Add a "where in" clause to the filter.
     *
     * @param  string $column
     * @param  mixed $values
     * @param  bool $not
     * @return $this
     */
    public function whereIn($column, $values, $not = false)
    {
        $this->where[$column][$not ? 'NotIn' : 'In'] = $values;

        return $this;
    }

    /**
     * Add a "where not in" clause to the filter.
     *
     * @param  string $column
     * @param  mixed $values
     * @return $this
     */
    public function whereNotIn($column, $values)
    {
        return $this->whereIn($column, $values, true);
    }

    /**
     * Add an "order by" clause to the filter.
     *
     * @param  string $column
     * @param  string $direction
     * @return $this
     */
    public function orderBy($column, $direction = 'asc')
    {
        $this->order_by[$column] = $direction;

        return $this;
    }

    /**
     * Set the "limit" value of the filter.
     *
     * @param  int $value
     * @return $this
     */
    public function limit($value)
    {
        $this->limit = $value;

        return $this;
    }

    /**
     * Set the "offset" value of the filter.
     *
     * @param  int $value
     * @return $this
     */
    public function offset($value)
    {
        $this->offset = $value;

        return $this;
    }

    /**
     * Return an array definition of the filtering
     *
     * @return array
     */
    public function toArray()
    {
        return array_filter(get_object_vars($this), function($value) {
            return !empty($value);
        });
    }

    /**
     * Return a query string definition of the filtering
     *
     * @return string
     */
    public function toQueryString()
    {
        return http_build_query($this->toArray());
    }

    /**
     * Prepare the value and operator for a where clause.
     *
     * @param  string $value
     * @param  string $operator
     * @param  bool $useDefault
     * @return array
     *
     * @throws \InvalidArgumentException
     */
    protected function prepareValueAndOperator($value, $operator, $useDefault = false)
    {
        if ($useDefault) {
            return [$operator, '='];
        }

        return [$value, $operator];
    }
}