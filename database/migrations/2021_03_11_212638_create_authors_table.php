<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAuthorsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('authors', function (Blueprint $table) {
            $table->increments('id');
            $table->string('profile_image_urls')->default('');
            $table->integer('pixiv_id')->default('0')->comment('P站ID');
            $table->string('name')->default('')->comment('昵称');
            $table->string('account')->default('')->comment('账号');
            $table->text('comment')->nullable()->comment('简介');
            $table->integer('total_follow_users')->default('0')->comment('粉丝数');
            $table->tinyInteger('is_priority_collect')->default('0')->comment('是否优先采集');
            $table->integer('collected_illust_date')->default('0')->comment('作品最后采集日期');
            $table->tinyInteger('is_collected_illust')->default('0')->comment('是否已采集作品');
            $table->string('background_image_url')->default('')->comment('主页背景图');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('authors');
    }
}
