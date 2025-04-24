<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('calculations', function (Blueprint $table) {
            $table->id();
            $table->decimal('principal', 12, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('years');
            $table->integer('months');
            $table->integer('compounding_frequency');
            $table->decimal('final_amount', 12, 2);
            $table->decimal('total_interest', 12, 2);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('calculations');
    }
};
