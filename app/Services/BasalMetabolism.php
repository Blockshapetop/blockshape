<?php

namespace App\Services;

class BasalMetabolism
{

    /**
     * Calculate basal metabolism rate
     * based on Harris-Benedict formula
     *
     * @param $weight
     * @param $height
     * @param $age
     * @return int
     */
    public function calculate($weight, $height, $age)
    {
        return (int) round(655 + (9.56 * $weight) + (1.85 * $this->toMeters($height)) - (4.68 * $age));
    }

    private function toMeters($height)
    {
        return round(($height / 100), 2);
    }
}


