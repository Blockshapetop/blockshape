<?php namespace App\Services;

class PressureGlossary
{
    protected $items = [
        'normotonic-hypertonic',
        'weakness-normotonic',
        'hypotonic-hypertonic',
        'normotonic-normotonic',
        'hipertonic-attention'
    ];

    public function all()
    {
        return $this->items;
    }
}
