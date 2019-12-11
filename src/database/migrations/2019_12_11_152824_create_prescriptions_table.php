<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePrescriptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('prescriptions', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('medical_report_id')->unsigned()->nullable();
            
            $table->foreign('medical_report_id')
                ->references('id')
                ->on('medical_reports')
                ->onDelete('set null');
            $table->integer('medicine_id')->unsigned()->nullable();
            $table->string('info')->nullable();
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
        Schema::dropIfExists('prescriptions');
    }
}
