<?php

use App\Enums\Apartment\ApartmentStatus;
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
        Schema::create('apartments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // owner
            $table->json('title');
            $table->json('description');
            $table->decimal('price', 10, 2);
            $table->string('currency'); // $ or € or SYP
            $table->float('rate')->default(0);
            $table->integer('governorate');
            $table->json('city');
            $table->json('address');
            $table->string('status')->default(ApartmentStatus::AVAILABLE->value); // enum: available, unavailable
            $table->json('availability')->nullable();
            $table->unsignedInteger('number_of_room');
            $table->unsignedInteger('number_of_bathroom');
            $table->unsignedInteger('area');
            $table->unsignedInteger('floor');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('apartments');
    }
};
