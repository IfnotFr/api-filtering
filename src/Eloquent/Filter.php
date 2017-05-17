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
        $this->options = array_merge(config('apifiltering.default'), $inputs);
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
            foreach ($this->options['where'] as $column => $details) {
                foreach ($details as $operator => $value) {
                    $query->where($column, $operator, $value);
                }
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
            $query->limit($this->options['limit']);
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
            $query->offset($this->options['offset']);
        }

        return $query;
    }
}