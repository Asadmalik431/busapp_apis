<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAgeToChildrens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('childrens', function (Blueprint $table) {
            $table->string('age')->nullable();
            $table->string('school')->nullable();
            $table->string('section')->nullable();
            $table->string('branch')->nullable();
            $table->string('date_of_bith')->nullable();
            $table->string('nic')->nullable();
            $table->string('present_address')->nullable();
            $table->string('permanent_address')->nullable();
            $table->string('special_handling')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('childrens', function (Blueprint $table) {
            $table->dropColumn('age')->nullable();
            $table->dropColumn('school')->nullable();
            $table->dropColumn('section')->nullable();
            $table->dropColumn('branch')->nullable();
            $table->dropColumn('date_of_bith')->nullable();
            $table->dropColumn('nic')->nullable();
            $table->dropColumn('present_address')->nullable();
            $table->dropColumn('permanent_address')->nullable();
            $table->dropColumn('special_handling')->nullable();
        });
    }
}
