<?php namespace App\Recipe;

abstract class Builder
{
    protected $query;

    public function find()
    {
        return $this->build()->first();
    }
}
