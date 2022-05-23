<?php

namespace App\Services;

class Discount
{
    private $price;
    private $discount;

    public function __construct($price, $discount)
    {
        $this->price = $price;
        $this->discount = $discount;
    }

    public function calculate()
    {
        return $this->price * ((100 - $this->discount) / 100);
    }
}

