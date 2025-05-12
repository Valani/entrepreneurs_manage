<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {

        Schema::create('report_entrepreneurs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_entrepreneurs')->constrained('entrepreneurs', 'id_entrepreneurs')->onDelete('cascade');
            $table->foreignId('id_report')->constrained('reports', 'id_report')->onDelete('cascade');
            $table->integer('quarter');
            $table->integer('year');
            $table->boolean('done')->default(false);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('report_entrepreneurs');
        Schema::dropIfExists('reports');
    }
};