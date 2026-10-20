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
        Schema::create('citizens', function (Blueprint $table) {
            $table->id();
            $table->string('identity_number', 11)->unique();
            $table->string('email')->unique();
            $table->string('password_hash');
            $table->string('full_name');
            $table->string('phone')->nullable();
            $table->unsignedBigInteger('person_id_sgp')->nullable()->index();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('status', 20)->default('unverified'); 
            $table->unsignedTinyInteger('failed_login_attempts')->default(0);
            $table->timestamp('locked_until')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('citizens');
    }
};
