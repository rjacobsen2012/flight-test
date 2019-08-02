<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatteriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasTable('batteries')) {
            Schema::create('batteries', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('battery_name');
                $table->string('battery_sn');
                $table->unsignedBigInteger('drone_id');
                $table->timestamps();

                $table->foreign('drone_id')->references('id')->on('drones');
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batteries');
    }
}
