<?php namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class RecipeProductTransformer extends TransformerAbstract
{
    public function transform($data)
    {
        return $data->toArray() + [
            'product' => $data->product->toArray()
        ];
    }
}
