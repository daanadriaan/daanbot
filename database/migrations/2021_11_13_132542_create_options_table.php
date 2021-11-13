<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOptionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('types', function (Blueprint $table) {
            $table->id();
            $table->string('label');
            $table->longText('content')->nullable();
        });
        Schema::create('option_type', function (Blueprint $table) {
            $table->integer('type_id')->index();
            $table->integer('option_id')->index();
            $table->string('label');
            $table->integer('index')->nullable();
        });
        Schema::create('options', function (Blueprint $table) {
            $table->id();
            $table->integer('redirect_to');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('options');
    }
}
