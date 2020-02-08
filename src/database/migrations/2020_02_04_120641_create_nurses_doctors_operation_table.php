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
        Schema::create('doctor_operations', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamps();
            $table->integer('operations_id')->unsigned()->nullable();
            $table->foreign('operations_id')
            ->references('id')
            ->on('operations')
            ->onDelete('set null');
            $table->integer('doctor_id')->unsigned()->nullable();
            $table->foreign('doctor_id')
            ->references('id')
            ->on('doctors')
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
        Schema::dropIfExists('doctor_operations');
    }
}
