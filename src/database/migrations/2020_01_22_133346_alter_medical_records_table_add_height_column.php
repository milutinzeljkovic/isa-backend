<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMedicalRecordsTableAddHeightColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('blood_type')->nullable();
            $table->string('diopter')->nullable();
            $table->string('allergy')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('medical_records', function (Blueprint $table) {
            $table->dropColumn('height')->nullable();
            $table->dropColumn('weight')->nullable();
            $table->dropColumn('blood_type')->nullable();
            $table->dropColumn('diopter')->nullable();
            $table->dropColumn('allergy')->nullable();

        });
    }
}
