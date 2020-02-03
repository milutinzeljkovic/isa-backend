<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointments', function (Blueprint $table) {
            $table->increments('id');
            $table->timestamp('date');	
            $table->integer('price');
            $table->boolean('done')->default(0);
            $table->integer('clinic_id')->unsigned()->nullable();
            $table->foreign('clinic_id')
            ->references('id')
            ->on('clinics')
            ->onDelete('set null');
            $table->integer('appointment_type_id')->unsigned()->nullable();
            $table->foreign('appointment_type_id')
            ->references('id')
            ->on('appointment_types')
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
            $table->timestamps();
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
        Schema::dropIfExists('appointments');
    }
}
