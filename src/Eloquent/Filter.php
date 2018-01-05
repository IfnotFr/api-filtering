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

    /**
     * Collection constructor.
     *
     * @param Builder $query
     * @param array $inputs
     */
    public function __construct(Builder $query, array $inputs = [])
    {
        $this->query = $query;

        $inputs = $this->parseInputJson($inputs);
        $this->options = array_merge(config('apifiltering.default', []), $inputs);
    }

    protected function parseInputJson($inputs)
    {
        foreach($inputs as $name => $content) {
            if(is_string($content)) {
                $inputs[$name] = json_decode($content, true);
            }
        }

        return $inputs;
    }

    /**
     * @return Builder
     */
    public function handle()
    {
        $query = $this->query;

        $query = $this->handleWhere($query);
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
                $query = $this->handleWhereCondition($query, $column, $condition);
            }
        }

        return $query;
    }

    protected function handleWhereCondition($query, $column, $condition)
    {
        // If there is a specified operator
        if(is_array($condition)) {
            foreach ($condition as $operator => $value) {
                $operator = strtolower($operator);

                if($operator == 'in') {
                    $query->whereIn($column, explode(',', $value));
                }
                elseif($operator == 'not in') {
                    $query->whereNotIn($column, explode(',', $value));
                }
                elseif($operator == 'between') {
                    $query->whereBetween($column, explode(',', $value));
                }
                else {
                    if($value == 'null') {
                        if($operator == '=') {
                            $query->whereNull($column);
                        } elseif($operator == '!=') {
                            $query->whereNotNull($column);
                        }
                    } else {
                        $query->where($column, $operator, $value);
                    }
                }
            }
        }
        // If there is no operator, assign the value with the equal operator
        else {
            if($condition == 'null') {
                $query->whereNull($column);
            } else {
                $query->where($column, '=', $condition);
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
