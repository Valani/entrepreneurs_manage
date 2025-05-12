<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up()
    {
        Schema::create('financial_data', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_entrepreneurs')->constrained('entrepreneurs', 'id_entrepreneurs')->onDelete('cascade');
            $table->date('date');
            $table->decimal('cash', 10, 2)->default(0);
            $table->decimal('non_cash', 10, 2)->default(0);
            $table->timestamps();

            // Add unique constraint to prevent duplicate entries for the same date and entrepreneur
            $table->unique(['id_entrepreneurs', 'date']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('financial_data');
    }
};