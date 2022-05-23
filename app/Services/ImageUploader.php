<?php namespace App\Services;

use App\Services\Contracts\ImageUploader as ImageUploaderContract;
use Closure;
use Illuminate\Filesystem\Filesystem;

class ImageUploader implements ImageUploaderContract
{
    public function upload($image, Closure $onSuccess = null, Closure $onFailure = null)
    {
        $file = $this->getTargetFileName($image);
        if ($status = (new Filesystem)->copy($image->getRealPath(), config('laravel-glide.source.path') . '/' . $file)) {
            if ($onSuccess) {
                call_user_func_array($onSuccess, [$file, $image]);
            }
        } else {
            if ($onFailure) {
                call_user_func_array($onFailure, [$file, $image]);
            }
        }

        return $status;
    }

    /**
     * @param $image
     * @return string
     */
    protected function getTargetFileName($image)
    {
        $hash = md5_file($image->getRealPath());
        $ext = $image->getClientOriginalExtension();
        $file = "{$hash}.{$ext}";

        return $file;
    }
}

