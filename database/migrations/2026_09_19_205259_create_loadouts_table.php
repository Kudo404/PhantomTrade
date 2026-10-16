<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('loadouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gear_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            // A user can only equip the same gear once
            $table->unique(['user_id', 'gear_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('loadouts');
    }
};