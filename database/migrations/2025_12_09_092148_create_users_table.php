<?php

use App\Enums\Role\RoleName;
use App\Enums\User\UserStatus;
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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('phone')->unique();
            $table->timestamp('verified_at')->nullable();
            $table->integer('status')->default(UserStatus::pending->value);
            $table->string('password');
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('username')->unique()->nullable();
            $table->date('date_of_birth')->nullable();
            $table->foreignId('role_id')->constrained('roles', 'id')->default(RoleName::user->value);
            $table->foreignId('id_photo')->nullable()->constrained('media', 'id')->nullOnDelete();
            $table->foreignId('personal_photo')->nullable()->constrained('media', 'id')->nullOnDelete();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};
