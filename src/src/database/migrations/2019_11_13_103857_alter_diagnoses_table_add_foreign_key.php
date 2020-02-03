<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDiagnosesTableAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->integer('clinical_center_id')->unsigned()->nullable();

            $table->foreign('clinical_center_id')
                ->references('id')
                ->on('clinical_centers')
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
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->dropForeign(['clinical_center_id']);
            $table->dropColumn('clinical_center_id');
        });
    }
}
