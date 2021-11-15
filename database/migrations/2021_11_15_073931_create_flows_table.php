<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateFlowsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('flows', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('type_id')->nullable();
            $table->timestamps();
        });

        Schema::table('types', function (Blueprint $table) {
            $table->integer('flow_id')->nullable();
            $table->float('x')->nullable();
            $table->float('y')->nullable();
        });

        Schema::table('options', function (Blueprint $table) {
            $table->float('x')->nullable();
            $table->float('y')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('flows');
    }
}