<?php

namespace App\Console;

use App\Admin\Repositories\SystemConfig;
use App\Console\Commends\Author;
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
        'author'    => Author::class,
    ];


    /**
     * @param Schedule $schedule
     * @throws \Throwable
     */
    protected function schedule(Schedule $schedule)
    {
        //爬虫开关
        $PSwitch = SystemConfig::getConfig(SystemConfig::REPTILE_SWITCH);
        if ($PSwitch == SystemConfig::ENABLE) {
            //采集作者信息
            $schedule->command('author')->everyMinute();
        }

    }


    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
