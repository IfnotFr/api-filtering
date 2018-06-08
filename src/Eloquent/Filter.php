<?php

namespace Ifnot\ApiFiltering\Eloquent;

use Illuminate\Database\Eloquent\Builder;

/**
 * Class Filter
 *
 * @package Ifnot\ApiFiltering\Eloquent
 */
class Filter
{
    protected $query;

    protected $options = [];

    protected $config = [];

    /**
     * Collection constructor.
     *
     * @param Builder $query
     * @param array $inputs
     */
    public function __construct(Builder $query, array $inputs = [], $config = [])
    {
        $this->query = $query;

        $inputs = $this->parseInputJson($inputs);
        $this->options = array_merge(config('apifiltering.default', []), $inputs);
        $this->config = $config;
        $this->casting = config('apifiltering.casting', []);
    }

    protected function parseInputJson($inputs)
    {
        foreach ($inputs as $name => $content) {
            if (is_string($content)) {
                $inputs[$name] = json_decode($content, true);
            }
        }

        return $inputs;
    }

    protected function castValue($value)
    {
        foreach ($this->casting as $casting) {
            list($search, $replace) = $casting;
            if ($value === $search) {
                $value = $replace;
            }
        }

        return $value;
    }

    /**
     * @return Builder
     */
    public function handle()
    {
        $query = $this->query;

        $query = $this->handleWhere($query);
        $query = $this->handleHaving($query);
        $query = $this->handleOrder($query);
        $query = $this->handleLimit($query);
        $query = $this->handleOffset($query);

        return $query;
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    protected function handleWhere($query)
    {
        if (! empty($this->options['where'])) {
            foreach ($this->options['where'] as $column => $condition) {
                $query = $this->handleWhereOrHavingCondition($query, $column, $condition, 'where');
            }
        }

        return $query;
    }

    /**
     * @param $query
     *
     * @return Builder
     */
    protected function handleHaving($query)
    {
        if (! empty($this->options['having'])) {
            foreach ($this->options['having'] as $column => $condition) {
                $query = $this->handleWhereOrHavingCondition($query, $column, $condition, 'having');
            }
        }

        return $query;
    }

    protected function handleWhereOrHavingCondition($query, $column, $condition, $type = 'where')
    {
        if(isset($this->config['prefix'])) {
            $column = $this->config['prefix'] . '.' . $column;
        }

        // Searching for a custom condition handler for this column
        if(isset($this->config['columns'])) {
            foreach($this->config['columns'] as $columnTest => $handler) {
                if (fnmatch($columnTest, $column)) {
                    if (is_array($condition)) {
                        foreach ($condition as $operator => $value) {
                            $handler($query, $column, $operator, $value, $type);
                        }
                    } else {
                        $handler($query, $column, '=', $condition, $type);
                    }
                    return $query;
                }
            }
        }

        // If there is a specified operator
        if (is_array($condition)) {
            foreach ($condition as $operator => $value) {
                $value = $this->castValue($value);

                $operator = strtolower($operator);

                if ($operator == 'in') {
                    $query->{$type . 'In'}($column, explode(',', $value));
                } elseif ($operator == 'not in') {
                    $query->{$type . 'NotIn'}($column, explode(',', $value));
                } elseif ($operator == 'between') {
                    $query->{$type . 'Between'}($column, explode(',', $value));
                } else {
                    if (is_null($value)) {
                        if ($operator == '=') {
                            $query->{$type . 'Null'}($column);
                        } elseif ($operator == '!=') {
                            $query->{$type . 'NotNull'}($column);
                        }
                    } else {
                        $query->{$type}($column, $operator, $value);
                    }
                }
            }
        } // If there is no operator, assign the value with the equal operator
        else {
            if (is_null($condition)) {
                $query->{$type . 'Null'}($column);
            } else {
                $query->{$type}($column, '=', $condition);
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function handleOrder(Builder $query)
    {
        if (! empty($this->options['order_by'])) {
            foreach ($this->options['order_by'] as $column => $direction) {
                $query->orderBy($column, $direction);
            }
        }

        return $query;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function handleLimit(Builder $query)
    {
        if (! empty($this->options['limit'])) {
            $query->limit((int) $this->options['limit']);
        }

        return $query;
    }

    /**
     * @param Builder $query
     *
     * @return Builder
     */
    protected function handleOffset(Builder $query)
    {
        if (! empty($this->options['offset'])) {
            $query->offset((int) $this->options['offset']);
        }

        return $query;
    }
}
