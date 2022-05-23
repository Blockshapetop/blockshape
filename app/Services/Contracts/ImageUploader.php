<?php


namespace App\Services\Contracts;

use Closure;

interface ImageUploader
{
    public function upload($image, Closure $onSuccess = null, Closure $onFailure = null);
}

