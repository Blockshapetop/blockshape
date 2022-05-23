<?php

namespace App\Traits;

use App\Recipe;
use App\RecipeAllergiesExclude;
use App\RecipeDiseasesExclude;
use App\RecipeFoodExclude;

trait FindRecipes
{
    /**
     * @return $this
     */
    protected function createQuery()
    {
        $this->query = Recipe::select('recipes.*')->with('nutrient');

        return $this;
    }

    /**
     * @return $this
     */
    protected function filterBySeason()
    {
        $this->query->whereRaw("(season IS NULL OR season LIKE '%" . $this->getSeason() . "%')");

        return $this;
    }

    protected function getSeason()
    {
        $seasons = [
            0 => 'winter',
            1 => 'spring',
            2 => 'summer',
            3 => 'autumn'
        ];

        return $seasons[floor(date('n') / 3) % 4];
    }

    protected function excludeDiseases()
    {
        if (! empty($diseases = $this->params->getDiseases()) && ($recipes = $this->getExcludesByDiseases($diseases))) {
            $this->query->whereNotIn('recipes.id', $recipes);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function excludeAllergies()
    {
        if (! empty($allergies = $this->params->getAllergies()) && ($recipes = $this->getExcludesByAllergies($allergies))) {
            $this->query->whereNotIn('recipes.id', $recipes);
        }

        return $this;
    }


    protected function excludeFood()
    {
        if (! empty($excludes = $this->params->getFoodExcludes()) && ($recipes = $this->getExcludesByNotConsumingFood($excludes))) {
            $this->query->whereNotIn('recipes.id', $recipes);
        }

        return $this;
    }

    /**
     * @return $this
     */
    protected function randomizeResults()
    {
        $this->query->orderBy(\DB::raw('RAND()'));

        return $this;
    }

    /**
     * @param $excludes
     * @return array
     */
    protected function getExcludesByNotConsumingFood($excludes)
    {
        return RecipeFoodExclude::whereIn('food_excludes_id', $excludes)->lists('recipe_id')->toArray();
    }

    /**
     * @param $diseases
     * @return array
     */
    protected function getExcludesByDiseases($diseases)
    {
        return RecipeDiseasesExclude::whereIn('disease_id', $diseases)->lists('recipe_id')->toArray();
    }

    /**
     * @param $allergies
     * @return array
     */
    protected function getExcludesByAllergies($allergies)
    {
        return RecipeAllergiesExclude::whereIn('allergy_id', $allergies)->lists('recipe_id')->toArray();
    }
}
