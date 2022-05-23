<?php namespace App\BMICalculator;

use Illuminate\Support\Manager as DriverManager;

class Manager extends DriverManager
{

    protected $defaultDriver = 'female';

    /**
     * Get the default driver name.
     *
     * @return string
     */
    public function getDefaultDriver()
    {
        return $this->defaultDriver;
    }

    public function createMaleDriver()
    {
        return new MaleCalculator();
    }

    public function createFemaleDriver()
    {
        return new FemaleCalculator();
    }
}
