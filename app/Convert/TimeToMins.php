<?php

namespace App\Convert;

class TimeToMins implements Convert
{
    protected $value;

    /**
     * TimeToMins constructor.
     *
     * @param $time
     */
    public function __construct($time)
    {
        list($hours, $minutes) = explode(':', $time);

        $this->value = $hours * 60 + $minutes;
    }

    public function convert()
    {
        return $this->value;
    }
}
