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
        Schema::create('tracking_events_cache', function (Blueprint $table) {
            $table->id();
            $table->foreignId('request_id')->constrained('portal_requests');
            $table->string('sgp_status', 30);
            $table->timestamp('sgp_last_transition_at')->nullable();
            $table->timestamp('fetched_at');
            $table->timestamps();
            $table->unique('request_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tracking_events_cache');
    }
};
