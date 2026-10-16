<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->dropForeign(['wanted_gear_id']);
            $table->dropColumn(['listing_type', 'wanted_gear_id']);
        });
    }

    public function down(): void
    {
        Schema::table('listings', function (Blueprint $table) {
            $table->string('listing_type')->default('buyout');
            $table->foreignId('wanted_gear_id')->nullable()->constrained('gears')->nullOnDelete();
        });
    }
};