<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKeysTable extends Migration
{
    public function up()
    {
        Schema::create('keys', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_entrepreneurs')->constrained('entrepreneurs', 'id_entrepreneurs')->onDelete('cascade');
            $table->enum('type', ['private', 'asc']);
            $table->date('date_start');
            $table->date('date_end');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('keys');
    }
}