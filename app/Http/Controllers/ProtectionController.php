<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\ProtectionDevice;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProtectionController extends Controller
{
    public function index()
    {
        return redirect('/map');
    }

    public function deploy(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'gear_id' => ['required', 'integer', 'exists:gears,id'],
            'latitude' => ['required', 'numeric', 'between:-90,90'],
            'longitude' => ['required', 'numeric', 'between:-180,180'],
            'label' => ['nullable', 'string', 'max:80'],
        ]);

        $gear = Gear::findOrFail($data['gear_id']);
        if (! in_array($gear->type, ['ward', 'anchor', 'trap', 'scanner'], true)) {
            return back()->withErrors(['gear_id' => 'This item cannot be deployed on the map.'])->withInput();
        }

        $device = DB::transaction(function () use ($user, $gear, $data) {
            $inventory = Inventory::where('user_id', $user->id)
                ->where('gear_id', $gear->id)
                ->where('quantity', '>', 0)
                ->lockForUpdate()
                ->first();

            if (! $inventory) {
                return null;
            }

            if ($inventory->quantity === 1) {
                $inventory->delete();
            } else {
                $inventory->decrement('quantity');
            }

            $radius = match ($gear->type) {
                'ward' => 100,
                'anchor' => 60,
                'scanner' => 50,
                'trap' => 30,
                default => 25,
            };

            return ProtectionDevice::create([
                'user_id' => $user->id,
                'gear_id' => $gear->id,
                'label' => ($data['label'] ?? null) ?: $gear->name,
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'radius_meters' => $radius,
                'battery_minutes' => 1440,
                'activated_at' => now(),
                'is_active' => true,
            ]);
        });

        if (! $device) {
            return back()->withErrors(['gear_id' => 'You must own this gear before deploying it.'])->withInput();
        }

        return redirect('/map')->with('success', $gear->name . ' deployed on the Haunt Map.');
    }

    public function replaceBattery(Request $request, $id)
    {
        $user = $request->user();
        $device = ProtectionDevice::where('user_id', $user->id)->findOrFail($id);

        $replaced = DB::transaction(function () use ($user, $device) {
            $batteryInventory = Inventory::where('user_id', $user->id)
                ->where('quantity', '>', 0)
                ->whereHas('gear', fn ($q) => $q->where('type', 'battery'))
                ->lockForUpdate()
                ->first();

            if (! $batteryInventory) {
                return false;
            }

            if ($batteryInventory->quantity === 1) {
                $batteryInventory->delete();
            } else {
                $batteryInventory->decrement('quantity');
            }

            $device->forceFill([
                'battery_minutes' => 1440,
                'activated_at' => now(),
                'is_active' => true,
            ])->save();

            return true;
        });

        if (! $replaced) {
            return redirect('/map')->withErrors(['battery' => 'You need a battery item from the Gear Shop to replace the power cell.']);
        }

        return redirect('/map')->with('success', 'Equipment battery replaced.');
    }

    public function recharge(Request $request, $id)
    {
        $user = $request->user();
        $device = ProtectionDevice::where('user_id', $user->id)->findOrFail($id);
        $cost = 10;

        $recharged = DB::transaction(function () use ($user, $device, $cost) {
            $lockedUser = \App\Models\User::whereKey($user->id)->lockForUpdate()->firstOrFail();
            if ((int) $lockedUser->ecto < $cost) {
                return false;
            }

            $lockedUser->spendEcto($cost);
            $device->forceFill([
                'battery_minutes' => 1440,
                'activated_at' => now(),
                'is_active' => true,
            ])->save();

            return true;
        });

        if (! $recharged) {
            return redirect('/map')->withErrors(['battery' => 'You need 10 Ecto to recharge this equipment.']);
        }

        return redirect('/map')->with('success', 'Equipment recharged for 10 Ecto.');
    }

    public function deactivate(Request $request, $id)
    {
        $device = ProtectionDevice::where('user_id', $request->user()->id)->findOrFail($id);
        $device->update(['is_active' => false]);

        return redirect('/map')->with('success', 'Equipment switched off.');
    }
}
