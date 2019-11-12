<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterMedicalReportsTableAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('medical_reports', function (Blueprint $table) {
            $table->integer('diagnose_id')->unsigned()->nullable();

            $table->foreign('diagnose_id')
                ->references('id')
                ->on('diagnoses')
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
        Schema::table('medical_reports', function (Blueprint $table) {
            $table->dropForeign(['diagnose_id']);
            $table->dropColumn('diagnose_id');
        });
    }
}
