<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donation_requests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('donation_id')->constrained()->cascadeOnDelete();
            $table->foreignId('upcycler_id')->constrained('users')->cascadeOnDelete();

            $table->text('message')->nullable();
            $table->string('status')->default('pending')->index();
            $table->timestamp('scheduled_at')->nullable();
            $table->string('scheduled_note')->nullable();
            $table->timestamp('responded_at')->nullable();
            $table->timestamp('completed_at')->nullable();

            $table->timestamps();

            // One open request per upcycler per donation.
            $table->unique(['donation_id', 'upcycler_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donation_requests');
    }
};
