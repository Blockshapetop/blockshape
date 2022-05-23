<?php namespace App\Traits\Models;

use App\Scopes\DescOrdersScope;

trait DescOrders
{
    public static function bootDescOrders()
    {
        static::addGlobalScope(new DescOrdersScope());
    }

    /**
     * Get a new query builder that only includes soft deletes.
     *
     * @return \Illuminate\Database\Eloquent\Builder|static
     */
    public static function unsorted()
    {
        $instance = new static;

        return $instance->newQueryWithoutScope(new DescOrdersScope);
    }
}

