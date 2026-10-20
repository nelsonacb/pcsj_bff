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
        Schema::create('portal_attachments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('draft_id')->nullable()->constrained('portal_drafts');
            $table->foreignId('request_id')->nullable()->constrained('portal_requests');
            $table->string('filename');
            $table->string('original_name');
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('size');
            $table->string('storage_path');
            $table->string('checksum', 64)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('portal_attachments');
    }
};
