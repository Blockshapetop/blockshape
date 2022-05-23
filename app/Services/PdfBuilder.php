<?php

namespace App\Services;

use App\Services\Contracts\PdfBuilder as PdfBuilderContract;
use App\User;
use App\UserHistory;
use Artisan;
use Illuminate\Filesystem\Filesystem;

/**
 * Class PdfBuilder
 *
 * @package App\Services
 */
class PdfBuilder implements PdfBuilderContract
{
    /**
     * @var Filesystem
     */
    private $filesystem;


    /**
     * PdfBuilder constructor.
     *
     * @param Filesystem $filesystem
     */
    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    /**
     * @param string $htmlDocument
     * @return string
     */
    public function build($htmlDocument)
    {
        $htmlFile = tempnam($tmpDir = sys_get_temp_dir(), '_html');
        $pdfFile = tempnam($tmpDir, '_pdf');

        $this->filesystem->put(
            $htmlFile,
            $htmlDocument
        );

        $this->convert($htmlFile, $pdfFile);

        return $this->filesystem->get($pdfFile);
    }

    private function convert($in, $out)
    {
        return Artisan::call("html2pdf", ['in' => $in, 'out' => $out]);
    }
}
