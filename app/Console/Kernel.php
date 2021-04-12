<?php

namespace App\Console;

use App\Admin\Repositories\SystemConfig;
use App\Console\Commends\Author;
use App\Console\Commends\AuthorIllusts;
use App\Console\Commends\IllustsImage;
use App\Console\Commends\IllustsRelated;
use App\Console\Commends\RankingDay;
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
        'author'            => Author::class,
        'author-illusts'    => AuthorIllusts::class,
        'illusts-image'     => IllustsImage::class,
        'illusts-related'   => IllustsRelated::class,
        'ranking-day'       => RankingDay::class,
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
            //采集作者作品
            $schedule->command('author-illusts')->everyMinute();
            //下载作品图片
            $schedule->command('illusts-image')->everyMinute();
            //采集相关作品
            $schedule->command('illusts-related')->everyMinute();
            //作品排行
            $schedule->command('ranking-day')->dailyAt('01:00');
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
