<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->string('hunter_name')->nullable()->after('location_name');
            $table->string('hunter_tier')->nullable()->after('hunter_name'); // D | C | B | A | S
            $table->string('license_number')->nullable()->unique()->after('hunter_tier');
            $table->timestamp('license_issued_at')->nullable()->after('license_number');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn([
                'hunter_name',
                'hunter_tier',
                'license_number',
                'license_issued_at',
            ]);
        });
    }
};