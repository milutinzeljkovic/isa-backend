<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterOperationsTableChangeOperationsRoomColumnName extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('operations', function(Blueprint $table)
        {
            $table->renameColumn('operations_rooms_id', 'operations_room_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('operations', function(Blueprint $table)
        {
            $table->renameColumn('operations_room_id', 'operations_rooms_id');

        });
    }
}
