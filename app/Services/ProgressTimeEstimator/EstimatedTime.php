<?php namespace App\Services\ProgressTimeEstimator;

class EstimatedTime
{
    private $min;
    private $max;

    public function __construct($min, $max)
    {
        $this->min = (int) $min;
        $this->max = (int) $max;
    }

    public function getValues()
    {
        return ($this->min == $this->max ? [$this->max] : [$this->min, $this->max]);
    }

    public function getAnabolicValues()
    {
        $periods = $this->decrementedValues();

        return $this->zeroLessValues($periods);
    }

    /**
     * @return array
     */
    private function decrementedValues()
    {
        return array_map(function ($period) {
            return max($period - 1, 0);
        }, [$this->min, $this->max]);
    }

    /**
     * @param $periods
     * @return array
     */
    private function zeroLessValues($periods)
    {
        return array_filter($periods, function ($period) {
            return $period > 0;
        });
    }
}

