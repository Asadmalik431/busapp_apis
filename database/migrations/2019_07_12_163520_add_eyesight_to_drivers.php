<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEyesightToDrivers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->string('eye_sight')->nullable();
            $table->string('medical_category')->nullable();
            $table->string('register_school')->nullable();
            $table->string('register_branch')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('drivers', function (Blueprint $table) {
            $table->dropColumn('eye_sight');
            $table->dropColumn('medical_category');
            $table->dropColumn('register_school');
            $table->dropColumn('register_branch');
        });
    }
}
