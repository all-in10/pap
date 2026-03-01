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
        Schema::create('user_push_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            
            // Push subscription data (from Web Push API)
            $table->text('endpoint'); // Unique per subscription
            $table->text('public_key'); // Server public key for encryption
            $table->text('auth_secret'); // Auth secret for encryption
            
            // Metadata
            $table->string('user_agent')->nullable(); // Browser/device info
            $table->string('platform')->nullable(); // ios, android, web
            $table->timestamp('last_checked_at')->nullable(); // Last successful validation
            $table->integer('failed_attempts')->default(0); // Track failures
            
            // Soft delete for marking as invalid without deleting
            $table->softDeletes();
            
            $table->timestamps();
            
            // Unique endpoint - prevent duplicates
            $table->unique('endpoint', 'idx_endpoint_unique');
            
            // Composite key for user + endpoint
            $table->index('user_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_push_subscriptions');
    }
};
