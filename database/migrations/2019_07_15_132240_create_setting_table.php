<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSettingTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('setting', function (Blueprint $table) {
            $table->bigIncrements('id')->comment('自增ID');
	        $table->integer('shifoushiyong',false,true)->comment('是否开启试用')->nullable();
	        $table->integer('time',false,true)->comment('试用时间(秒)')->nullable();
	        $table->integer('quanjujiami',false,true)->comment('开启全局加密输出')->nullable();
            $table->integer('urlsign',false,true)->comment('开启登录验证URL签名')->nullable();
	        $table->string('banben')->comment('软件版本')->nullable();
	        $table->string('dlldown')->comment('DLL路径')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('setting');
    }
}
