<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDiagnosesTableDropDescriptionAndClinicalCenterIdColumns extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('diagnoses', function (Blueprint $table) {
            $table->dropColumn('description');
            $table->dropForeign(['clinical_center_id']);
            $table->dropColumn('clinical_center_id');

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
            $table->dropColumn('description');
            $table->dropForeign(['clinical_center_id']);
            $table->dropColumn('clinical_center_id');

        });
    }
}
