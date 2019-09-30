<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddLatitudeToTblStations extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tbl_stations', function (Blueprint $table) {
            $table->string('latitude');
            $table->string('longitude');
            $table->string('route')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tbl_stations', function (Blueprint $table) {
            Schema::dropIfExists('latitude');
            Schema::dropIfExists('longitude');
            Schema::dropIfExists('route')->nullable();
        });
    }
}
