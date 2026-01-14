<?php

use App\Enums\Reservation\ReservationStatus;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservation_modifications', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('reservation_id')->constrained()->onDelete('cascade');
            $table->enum('type', ['end_date', 'start_date', 'total_amount', 'cancel'])->index();
            $table->text('old_value')->nullable();
            $table->text('new_value')->nullable();
            $table->string('status')->default(ReservationStatus::PENDING->value);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservation_modifications');
    }
};
