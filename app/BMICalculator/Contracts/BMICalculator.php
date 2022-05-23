<?php namespace App\BMICalculator\Contracts;

interface BMICalculator
{

    const BMI_UNDERWEIGHT        = 'underweight';

    const BMI_OVERWEIGHT         = 'overweight';

    const BMI_HEAVILY_OVERWEIGHT = 'heavily_overweight';

    const BMI_NORMAL             = 'normal';

    /**
     * Calculate bmi based on provided measurements
     *
     * @param int $height
     * @param int $weight
     * @return float
     */
    public function calculate($height, $weight);
}

