<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicineTherapyTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medicine_therapy', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('therapy_id')->unsigned()->nullable();
            $table->foreign('therapy_id')
                ->references('id')
                ->on('therapies')
                ->onDelete('set null');
            $table->integer('medicine_id')->unsigned()->nullable();
            $table->foreign('medicine_id')
                ->references('id')
                ->on('medicines')
                ->onDelete('set null');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('medicine_therapy');
    }
}
