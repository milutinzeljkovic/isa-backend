<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePatientClinicTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('patient_clinic', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
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
        Schema::dropIfExists('patient_clinic');
    }
}
