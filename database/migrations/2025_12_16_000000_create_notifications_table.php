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
            $table->id();
            $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
            $table->string('type')->index(); // e.g., 'reservation_approved', 'apartment_listed', etc.
            $table->string('title');
            $table->text('body');
            $table->json('data')->nullable(); // Additional data for the notification
            $table->boolean('read')->default(false);
            $table->timestamp('read_at')->nullable();
            // FCM related fields for future use
            $table->string('fcm_token')->nullable(); // User's FCM token
            $table->boolean('fcm_sent')->default(false); // Whether FCM notification was sent
            $table->timestamp('fcm_sent_at')->nullable();
            $table->text('fcm_error')->nullable(); // Store FCM errors if any
            
            $table->timestamps();
            
            // Indexes for better query performance
            $table->index(['user_id', 'read']);
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

