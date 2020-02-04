<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNursesDoctorsOperationTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('nurses_doctors_operation', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('operation_id')->unsigned()->nullable();
            $table->foreign('operation_id')
            ->references('id')
            ->on('operations')
            ->onDelete('set null');
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

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('nurses_doctors_operation');
    }
}
