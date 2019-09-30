<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDriverBusRelationsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('driver_bus_relations', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('driver_id');
            $table->integer('bus_id');
            $table->integer('route_id');
            $table->integer('child_id')->nullable();
            $table->string('book_time');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('driver_bus_relations');
    }
}
