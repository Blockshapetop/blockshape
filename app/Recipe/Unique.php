<?php namespace App\Recipe;

class Unique extends Builder implements RecipeBuilder
{
    /**
     * @var RecipeBuilder
     */
    private $builder;

    /**
     * @var array
     */
    private $exclude;


    /**
     * Unique constructor.
     *
     * @param RecipeBuilder $builder
     * @param array         $exclude
     */
    public function __construct(RecipeBuilder $builder, array $exclude = [])
    {
        $this->builder = $builder;
        $this->exclude = $exclude;
    }

    public function build()
    {
        $this->query = $this->builder->build();

        if (! empty($this->exclude)) {
            $this->query->whereNotIn('recipes.id', (array) $this->exclude);
        }

        return $this->query;
    }
}
