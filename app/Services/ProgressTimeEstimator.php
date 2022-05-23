<?php namespace App\Services;

use App\Services\ProgressTimeEstimator\EstimatedTime;

class ProgressTimeEstimator
{
    const PROGRESS_KG_PER_MONTH = 3;

    /**
     * Estimate in how many months you can lose weight
     * from current to recommended one
     *
     * @param $currentWeight
     * @param $recommendedWeight
     * @return float
     */
    public function estimate($currentWeight, $recommendedWeight)
    {
        $time = max(0, (($currentWeight - $recommendedWeight) / static::PROGRESS_KG_PER_MONTH));
        $min = floor($time);
        $max = ceil($time);

        return new EstimatedTime($min, $max);
    }
}
