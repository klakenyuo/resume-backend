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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('first_name');
            $table->string('last_name');
            $table->string('email')->unique();
            $table->string('telephone')->nullable();
            $table->string('address')->nullable();
            $table->date('birth_date')->nullable();
            $table->integer('exp_years')->nullable();
            $table->string('role')->default('user');
            $table->string('linkedin')->nullable();
            $table->string('verification_code')->nullable();
            $table->integer('isActive')->default(1);
            $table->integer('isAdmin')->default(0);
            $table->string('password');
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
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
