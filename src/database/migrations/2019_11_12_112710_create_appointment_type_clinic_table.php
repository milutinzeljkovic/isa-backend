<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAppointmentTypeClinicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('appointment_type_clinic', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('appointment_type_id')->unsigned()->nullable();
            $table->foreign('appointment_type_id')
                ->references('id')
                ->on('appointment_types')
                ->onDelete('set null');
            $table->integer('clinic_id')->unsigned()->nullable();
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
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
        Schema::dropIfExists('clinics_appointment_types');
    }
}
