<?php namespace App\BMICalculator;

use App\Imc;

class EloquentProvider
{
    public function find(BMIValue $bmiValue)
    {
        return Imc::where('value_min', '<=', $value = $bmiValue->getValue())->where('value_max', '>=', $value)->first();
    }
}
