<?php

namespace App\Nutrition\Rest;

class WeightLoss extends AbstractDriver
{
    protected $rules = [
        '07:00' => 'main:proteins-carbohydrates|carbohydrates',
        '11:00' => 'snack:carbohydrates',
        '14:00' => 'main:proteins-carbohydrates',
        '16:00' => 'snack:carbohydrates|proteins',
        //'18:00' => 'main:carbohydrates|vegetables-carbohydrates|proteins-carbohydrates'
        '18:00' => 'main:carbohydrates|proteins-carbohydrates'
    ];
}


