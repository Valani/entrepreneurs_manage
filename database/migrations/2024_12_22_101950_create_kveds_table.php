<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKvedsTable extends Migration
{
    public function up()
    {
        Schema::create('kveds', function (Blueprint $table) {
            $table->id('id_kved');
            $table->string('number')->unique();
            $table->string('name');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kveds');
    }
}