<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperationsRoomsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operations_rooms', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('number');
            $table->string('name');
            $table->integer('clinic_id')->unsigned()->nullable();
            $table->timestamps();
            $table->boolean('reserved')->default(0);
            $table->foreign('clinic_id')
            ->references('id')
            ->on('clinics')
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
        Schema::dropIfExists('operation_rooms');
    }
}
