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
        Schema::create('apartment_photos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('apartment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('medium_id')->constrained()->cascadeOnDelete();
            $table->unsignedInteger('order')->default(0);
            $table->boolean('is_main')->default(false);
            $table->unique(['apartment_id', 'medium_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartment_photos');
    }
};
