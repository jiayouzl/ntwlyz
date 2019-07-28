<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCardsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cards', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('card');
            $table->string('password');
            $table->tinyInteger('type');//1=天卡,2=周卡,3=月卡,4=季卡,5=半年卡,6=年卡
            $table->tinyInteger('consume');
            $table->string('key')->nullable();
            $table->dateTime('consumetime')->nullable();
            $table->tinyInteger('beifeng');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('cards');
    }
}
