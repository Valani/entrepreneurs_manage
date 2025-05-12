<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEntrepreneursTable extends Migration
{
    public function up()
    {
        Schema::create('entrepreneurs', function (Blueprint $table) {
            $table->id('id_entrepreneurs');
            $table->string('name');
            $table->string('ipn')->unique();
            $table->string('iban');
            $table->string('tax_office_name');
            $table->string('group');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('entrepreneurs');
    }
}