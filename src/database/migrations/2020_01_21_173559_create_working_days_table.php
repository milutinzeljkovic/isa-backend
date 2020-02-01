<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateWorkingDaysTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('working_days', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            
            $table->integer('doctor_id')->unsigned()->nullable();

            $table->foreign('doctor_id')
                ->references('id')
                ->on('doctors')
                ->onDelete('set null');

            $table->integer('nurse_id')->unsigned()->nullable();

            $table->foreign('nurse_id')
                ->references('id')
                ->on('nurses')
                ->onDelete('set null');

            $table->integer('day');
            $table->integer('from');
            $table->integer('to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('working_days');
    }
}
