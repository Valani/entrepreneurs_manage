<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateKvedEntrepreneursTable extends Migration
{
    public function up()
    {
        Schema::create('kved_entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_entrepreneurs')->constrained('entrepreneurs', 'id_entrepreneurs')->onDelete('cascade');
            $table->foreignId('id_kved')->constrained('kveds', 'id_kved')->onDelete('cascade');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('kved_entrepreneurs');
    }
}