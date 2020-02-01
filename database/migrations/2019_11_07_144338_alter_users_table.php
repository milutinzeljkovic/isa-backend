<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('ensurance_id');
            $table->string('phone_number',10);
            $table->string('last_name');
            $table->string('address');
            $table->string('city');
            $table->string('state');
            $table->boolean('confirmed')->default(0);
            $table->integer('userable_id');
            $table->string('userable_type');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('ensurance_id');
            $table->dropColumn('phone_number');
            $table->dropColumn('last_name');
            $table->dropColumn('address');
            $table->dropColumn('city');
            $table->dropColumn('state');
            $table->dropColumn('confirmed');
            $table->dropColumn('userable_id');
            $table->dropColumn('userable_type');
        });
    }
}
