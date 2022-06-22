<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateInterpreterTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('interpretables', function (Blueprint $table) {
            $table->id();
            $table->string('suggestion')->nullable(); // triggers when under 50%
            $table->integer('flow_id')->nullable(); // triggers when above 50%
            $table->integer('counter')->default(0);
            $table->timestamps();
        });
        Schema::create('interpreter_queries', function (Blueprint $table) {
            $table->id();
            $table->integer('interpretable_id')->index();
            $table->string('query');
            $table->integer('percentage')->default(10);
            $table->integer('counter')->default(0);
            $table->timestamps();
        });
    }
}
