<?php namespace App\Services;

use App\Recipe;
use App\Recipe\Params;
use App\Services\Contracts\RecipeFinder as RecipeFinderContract;
use App\Traits\FindRecipes;

class RecipeFinder implements RecipeFinderContract
{
    use FindRecipes;

    protected $query;

    protected $params;

    /**
     * @param Params $params
     * @return mixed
     */
    public function find(Params $params)
    {
        $this->params = $params;

        $this->createQuery()
            ->filterByNutrient()
            ->filterByEatingNumber()
            ->filterByPlacement()
            ->filterBySnack()
            ->filterBySeason()
            ->filterByTarget()
            ->excludeDiseases()
            ->excludeAllergies()
            ->excludeFood()
            ->makeListUnique()
            ->randomizeResults();

        return $this->query->first();
    }

    /**
     * @return $this
     */
    private function makeListUnique()
    {
        if (! empty($disables = $this->params->getDisabled())) {
            $this->query->whereNotIn('recipes.id', (array) $disables);
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function filterBySnack()
    {
        $this->query->where('snack', (int) $this->params->isSnack());

        return $this;
    }

    /**
     * Some products recommended for use before or after workout
     *
     * @return $this
     */
    private function filterByPlacement()
    {
        if ($this->params->isSnack()) {
            return $this;
        }

        if ($placement = $this->params->getPlacement()) {
            $this->query = $this->query->whereRaw("(placement IS NULL OR placement='" . $placement . "')");
        }

        return $this;
    }

    /**
     * Some products can be used only at specific num of eating (morning, day, evening, etc...)
     *
     * @return $this
     */
    private function filterByEatingNumber()
    {
        if ($this->params->isSnack()) {
            return $this;
        }

        if ($num = $this->params->getEatingNum()) {
            $target = $this->params->forTarget();

            /**
             * @hack: since in backend there is no way to mark eating num bigger then 3
             * just set it to max available (3)
             */
            $num = min($num, 3);

            if ($target && 'weight-gain' == $target) {
                $this->query = $this->query->whereRaw("(eating_gain is NULL OR FIND_IN_SET('{$num}', eating_gain))");
            } else {
                $this->query = $this->query->whereRaw("(eating is NULL OR FIND_IN_SET('{$num}', eating))");
            }
        }

        return $this;
    }

    /**
     * @return $this
     */
    private function filterByNutrient()
    {
        $this->query->join('nutrients', function ($join) {
            $join->on('nutrients.id', '=', 'recipes.nutrient_id')
                ->where('nutrients.slug', '=', $this->params->getNutrient());
        });

        return $this;
    }

    private function filterByTarget()
    {
        if ($target = $this->params->forTarget()) {
            $this->query->whereRaw("(targets is NULL OR FIND_IN_SET('{$target}', targets))");
        }

        return $this;
    }

    /**
     * @return mixed
     */
    public function getQuery()
    {
        return [
            $this->query->toSql(),
            $this->query->getBindings()
        ];
    }
}
