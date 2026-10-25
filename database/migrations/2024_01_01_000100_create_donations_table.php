<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('donations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();

            $table->string('title');
            $table->text('description');
            $table->string('category')->index();     // denim, cotton, linen, knit, blend, other
            $table->string('condition');             // new, gently_used, wearable, flawed, scrap
            $table->decimal('weight_kg', 6, 2)->default(0);
            $table->unsignedInteger('item_count')->default(1);

            $table->string('barangay')->index();     // Lapu-Lapu barangay-level matching
            $table->string('handoff_method')->default('pickup');
            $table->string('pickup_address')->nullable();
            $table->date('available_from')->nullable();

            $table->string('status')->default('listed')->index();
            $table->unsignedInteger('views')->default(0);

            $table->timestamps();

            $table->index(['status', 'category', 'barangay']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('donations');
    }
};
