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
        Schema::create('compound_interest_calculations', function (Blueprint $table) {
            $table->id();
            $table->decimal('principal', 10, 2);
            $table->decimal('interest_rate', 5, 2);
            $table->integer('time_period');
            $table->string('compounding_frequency');
            $table->decimal('result', 10, 2);
            $table->date('start_date');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('compound_interest_calculations');
    }
};
