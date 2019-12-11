<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterPrescriptionsTableAddNurseForeignKey extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->integer('nurse_id')->unsigned()->nullable();
            
            $table->foreign('nurse_id')
                ->references('id')
                ->on('nurses')
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
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropForeign(['nurse_id']);
            $table->dropColumn('nurse_id');
        });
    }
}
