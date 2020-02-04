<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('clinic_id')->unsigned()->nullable();
            $table->foreign('clinic_id')
            ->references('id')
            ->on('clinics')
            ->onDelete('set null');
            $table->integer('operations_rooms_id')->unsigned()->nullable();
            $table->foreign('operations_rooms_id')
            ->references('id')
            ->on('operations_rooms')
            ->onDelete('set null');
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')
            ->references('id')
            ->on('patients')
            ->onDelete('set null');
            $table->integer('lock_version')->default(1);
            $table->string('duration')->nullable();


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operations');
    }
}
