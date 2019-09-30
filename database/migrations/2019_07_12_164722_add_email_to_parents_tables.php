<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddEmailToParentsTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('parents_tables', function (Blueprint $table) {
            $table->string('contact')->nullable();
            $table->string('email')->nullable();
            $table->string('nicNumber')->nullable();
            $table->string('qualification')->nullable();
            $table->string('occupation')->nullable();
            $table->string('homeAddress')->nullable();
            $table->string('officeAddress')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('parents_tables', function (Blueprint $table) {
            $table->dropColumn('contact')->nullable();
            $table->dropColumn('email')->nullable();
            $table->dropColumn('nicNumber')->nullable();
            $table->dropColumn('qualification')->nullable();
            $table->dropColumn('occupation')->nullable();
            $table->dropColumn('homeAddress')->nullable();
            $table->dropColumn('officeAddress')->nullable();
        });
    }
}
