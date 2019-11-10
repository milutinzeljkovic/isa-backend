<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterClinicAdminsTableAddForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('clinic_admins', function (Blueprint $table) {
            $table->integer('clinic_id')->unsigned()->nullable();

            $table->foreign('clinic_id')
                ->references('id')
                ->on('clinics')
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
        Schema::table('clinic_admins', function (Blueprint $table) {
            $table->dropForeign(['clnic_id']);
            $table->dropColumn('clinic_id');
        });
    }
}
