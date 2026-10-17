<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('protection_devices', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('gear_id')->constrained()->cascadeOnDelete();
            $table->string('label')->nullable();
            $table->decimal('latitude', 10, 7);
            $table->decimal('longitude', 10, 7);
            $table->unsignedInteger('radius_meters')->default(100);
            $table->unsignedInteger('battery_minutes')->default(1440);
            $table->timestamp('activated_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index(['user_id', 'is_active']);
        });

        // Add a purchasable battery to the existing gear shop without overwriting admin data.
        if (Schema::hasTable('gears') && ! DB::table('gears')->where('type', 'battery')->exists()) {
            DB::table('gears')->insert([
                'name' => 'Standard Spectral Battery',
                'type' => 'battery',
                'description' => 'A replacement power cell for deployed spectral protection devices. One battery powers a device for 24 hours.',
                'ecto_cost' => 15,
                'energy_cost' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('protection_devices');
        // Keep the shop item and any user inventory safe if this migration is rolled back.
    }
};
