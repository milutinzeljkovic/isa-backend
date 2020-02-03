<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClinicalCenterAdminsTableAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinical_center_admins', function (Blueprint $table) {
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
        Schema::table('clinical_center_admins', function (Blueprint $table) {
            $table->dropForeign(['clinical_center_id']);
            $table->dropColumn('clinical_center_id');
        });
    }
}
