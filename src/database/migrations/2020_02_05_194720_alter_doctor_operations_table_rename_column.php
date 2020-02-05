<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterDoctorOperationsTableRenameColumn extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('doctor_operations', function (Blueprint $table) {
            $table->renameColumn('operation_id', 'operations_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('doctor_operations', function (Blueprint $table) {
            $table->renameColumn('operation_id', 'operations_id');
        });
    }
}
