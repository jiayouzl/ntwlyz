<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateReplaceLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('replace_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('key1')->comment('被转绑的机器码');
            $table->string('key2')->comment('待转绑的机器码');
            $table->string('day')->comment('转移天数');
            $table->dateTime('replacetime')->comment('操作时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('replace_logs');
    }
}
