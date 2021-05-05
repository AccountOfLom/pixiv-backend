<?php


namespace App\Console\Commends;


use App\Console\Common;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

/**
 * 插画随机排序
 * Class Ranging
 * @package App\Console\Commends
 */
class IllustSortRand extends Command
{

    use Common;

    /**
     * 控制台命令 signature 的名称。
     *
     * @var string
     */
    protected $signature = 'illust-sort-rand';

    /**
     * 控制台命令说明。
     *
     * @var string
     */
    protected $description = 'illust-sort-rand';


    /**
     * 执行控制台命令
     * @return bool
     * @throws \Throwable
     */
    public function handle()
    {
        $sql = "UPDATE `illustrations` SET `sort_rand` = CEILING(RAND()*99)";
        DB::select($sql);
    }
}