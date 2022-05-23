<?php namespace App\Recipe;

use Illuminate\Database\Query\Builder as QueryBuilder;

interface RecipeBuilder
{
    /**
     * Build a complex query
     *
     * @return QueryBuilder
     */
    public function build();

    /**
     * Find records
     *
     * @return mixed
     */
    public function find();
}
