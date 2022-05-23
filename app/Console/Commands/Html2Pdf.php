<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class Html2Pdf extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'html2pdf {in : Html file} {out : Pdf file}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Conver html document to pdf.';

    /**
     * Create a new command instance.
     *
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $binary = env('WKHTMLTOPDF', '/usr/local/bin/wkhtmltopdf');
        $command = "cat {$this->argument('in')} | {$binary} - {$this->argument('out')}";

        exec($command, $output);

        return join("", $output);
    }
}

