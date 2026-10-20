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
        Schema::create('portal_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('citizen_id')->constrained('citizens');
            $table->string('portal_number', 30)->unique(); 
            $table->string('type', 30);
            $table->unsignedBigInteger('sgp_resource_id')->nullable()->index();
            $table->string('sgp_resource_type', 40)->nullable(); 
            $table->string('status', 30)->default('draft'); 
            $table->json('submission_payload');
            $table->timestamp('submitted_at')->nullable();
            $table->timestamp('accepted_at')->nullable();
            $table->text('rejection_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_requests');
    }
};
