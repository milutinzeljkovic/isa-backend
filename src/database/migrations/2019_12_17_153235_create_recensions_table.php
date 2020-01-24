<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRecensionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recensions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('clinic_id')->unsigned()->nullable();
            $table->integer('patient_id')->unsigned()->nullable();
            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
                ->onDelete('set null');
            $table->foreign('patient_id')
                ->references('id')
                ->on('patients')
                ->onDelete('set null');
            $table->string('stars_count');	
            $table->unique(['patient_id','clinic_id','stars_count']);

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
        Schema::dropIfExists('recensions');
    }
}
