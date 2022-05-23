<?php

namespace App\Console;

use App\Console\Commands\DumpTranslations;
use App\Console\Commands\Html2Pdf;
use App\Console\Commands\ImportRecipes;
use App\Console\Commands\ImportReferences;
use App\Console\Commands\Inspire;
use App\Console\Commands\LoadTranslations;
use App\Console\Commands\ResetTranslations;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        Inspire::class,
        ImportReferences::class,
        ImportRecipes::class,
        LoadTranslations::class,
        ResetTranslations::class,
        DumpTranslations::class,
        Html2Pdf::class
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->command('inspire')
                 ->hourly();
    }
}

