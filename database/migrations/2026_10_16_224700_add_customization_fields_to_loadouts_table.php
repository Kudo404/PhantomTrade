<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('loadouts', function (Blueprint $table) {
            $table->string('slot', 32)->nullable()->after('gear_id'); // ward, anchor, trap, scanner
            $table->string('tuning_mode', 32)->default('balanced')->after('slot'); // balanced, overclocked, shielded, stealth
            $table->integer('frequency_khz')->default(440)->after('tuning_mode'); // Spectral frequency in Hz/kHz
            $table->integer('ward_reinforcement')->default(0)->after('frequency_khz'); // Bonus shield % (0-50%)
            $table->string('custom_name', 80)->nullable()->after('ward_reinforcement'); // Hunter's custom rig name
        });
    }

    public function down(): void
    {
        Schema::table('loadouts', function (Blueprint $table) {
            $table->dropColumn([
                'slot',
                'tuning_mode',
                'frequency_khz',
                'ward_reinforcement',
                'custom_name',
            ]);
        });
    }
};
