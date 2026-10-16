<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wanted_responses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('wanted_post_id')->constrained()->cascadeOnDelete();
            $table->foreignId('responder_id')->constrained('users')->cascadeOnDelete();
            $table->integer('offered_quantity');
            $table->integer('price_per_unit');
            $table->text('note')->nullable();
            $table->string('status')->default('pending'); // pending | accepted | rejected
            $table->timestamp('responded_at')->nullable();
            $table->timestamps();

            $table->index(['wanted_post_id', 'status']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wanted_responses');
    }
};