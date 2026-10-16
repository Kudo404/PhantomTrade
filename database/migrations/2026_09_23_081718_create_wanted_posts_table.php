<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('wanted_posts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gear_id')->constrained()->cascadeOnDelete();
            $table->integer('quantity');
            $table->integer('price_per_unit');
            $table->text('note')->nullable();
            $table->string('status')->default('open');   // open | fulfilled | cancelled
            $table->timestamps();

            $table->index(['status', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('wanted_posts');
    }
};