<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('trade_proposals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('listing_id')->constrained()->cascadeOnDelete();
            $table->foreignId('buyer_id')->constrained('users')->cascadeOnDelete();
            $table->foreignId('offered_gear_id')->constrained('gears')->cascadeOnDelete();
            $table->integer('offered_quantity');
            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // pending | accepted | rejected
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['listing_id', 'status']);
            $table->index(['buyer_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('trade_proposals');
    }
};