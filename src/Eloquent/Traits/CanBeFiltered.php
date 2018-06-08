<?php

namespace Ifnot\ApiFiltering\Eloquent\Traits;

use Ifnot\ApiFiltering\Eloquent\Filter;

/**
 * Class CanBeFiltered
 * @package Ifnot\ApiFiltering\Eloquent\Traits
 */
trait CanBeFiltered
{
    public function scopeFilter($query, $inputs = [], $columnConditions = [])
    {
        return (new Filter($query, $inputs, $columnConditions))->handle();
    }
}