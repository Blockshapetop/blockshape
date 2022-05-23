<?php namespace App\Nutrition\Rest;

abstract class AbstractDriver
{
    protected $rules;

    public function schedule()
    {
        $schedule = [];

        foreach ($this->rules as $time => $nutrients) {
            $schedule[$time] = $this->extractNutrient($time);
        }

        return $schedule;
    }

    /**
     * @param $shiftHours
     * @return mixed
     */
    protected function extractNutrient($shiftHours)
    {
        list($type, $nutrients) = explode(':', $this->rules[$shiftHours]);
        $options = explode('|', $nutrients);
        $nutrient = $options[array_rand($options)];

        return "{$type}:{$nutrient}";
    }
}

