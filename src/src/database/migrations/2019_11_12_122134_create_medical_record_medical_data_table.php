<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMedicalRecordMedicalDataTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('medical_record_medical_data', function (Blueprint $table) {
            $table->increments('id');
            $table->string('value');
            $table->integer('medical_record_id')->unsigned()->nullable();
            $table->foreign('medical_record_id')
                ->references('id')
                ->on('medical_records')
                ->onDelete('set null');
            $table->integer('medical_data_id')->unsigned()->nullable();
            $table->foreign('medical_data_id')
                ->references('id')
                ->on('medical_datas')
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
        Schema::dropIfExists('medical_record_medical_data');
    }
}
