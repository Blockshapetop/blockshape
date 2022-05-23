<?php namespace App\Services;

use App\Recipe\Params;
use App\Services\Contracts\RecipeFinder as RecipeFinderContract;
use App\Traits\FindRecipes;
use Illuminate\Support\Collection;

class RecipeTwoStepsFinder implements RecipeFinderContract
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
        static $items = null;

        $this->params = $params;

        if (null === $items) {

            $this->createQuery()
                ->filterBySeason()
                ->excludeDiseases()
                ->excludeAllergies()
                ->excludeFood()
                ->randomizeResults();

            $items = $this->query->get();
        }

        return $items->filter(function ($item) {
            return
                $this->filterBySnack($item)
                && $this->filterByPosition($item)
                && $this->filterByNutrient($item)
                && $this->filterByEatingNumber($item)
                && $this->itemDoesNotExistsInList($item);
        })->first();


    }

    private function filterBySnack($item)
    {
        return $item->snack == (int) $this->params->isSnack();
    }

    private function filterByPosition($item)
    {
        if ($placement = $this->params->getPlacement()) {
            is_null($item->placement) || $item->placement == $placement;
        }

        return true;
    }

    private function filterByNutrient($item)
    {
        return $item->nutrient->slug == $this->params->getNutrient();
    }

    private function filterByEatingNumber($item)
    {
        if ($num = $this->params->getEatingNum()) {
            return is_null($item->eating) || in_array($num, explode(',', $item->eating));
        }

        return true;
    }

    private function itemDoesNotExistsInList($item)
    {
        $disabled = Collection::make($this->params->getDisabled());

        return ! $disabled->contains($item->id);
    }
}

