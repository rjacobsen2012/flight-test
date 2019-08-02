<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGpsFramesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('gps_frames', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->dateTime('timestamp');
            $table->unsignedBigInteger('drone_id');
            $table->decimal('lat', 9, 6);
            $table->decimal('long', 9, 6);
            $table->decimal('alt', 9, 1);
            $table->timestamps();

            $table->foreign('drone_id')->references('id')->on('drones');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gps_frames');
    }
}
