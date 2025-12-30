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
        Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->integer('type')->index(); // e.g., 'reservation_approved', 'apartment_listed', etc.
            $table->json('title');
            $table->json('body');
            $table->json('data')->nullable(); // Additional data for the notification
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['user_id', 'read_at']);
            $table->index(['user_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notifications');
    }
};

