<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatteryFramesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('battery_frames', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('timestamp');
            $table->unsignedBigInteger('battery_id');
            $table->decimal('battery_percent', 9, 1);
            $table->decimal('battery_temperature', 9, 1);
            $table->timestamps();

            $table->foreign('battery_id')->references('id')->on('batteries');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('battery_frames');
    }
}
