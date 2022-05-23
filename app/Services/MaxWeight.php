<?php namespace App\Services;

use App\BMICalculator\ValueByAge;

class MaxWeight
{
    public function calculate($height, $age)
    {
        return ceil(pow($this->toMeters($height), 2) * (new ValueByAge)->get($age));
    }

    private function toMeters($height)
    {
        return round($height/100, 2);
    }
}

