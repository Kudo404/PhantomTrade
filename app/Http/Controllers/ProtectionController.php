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
        $user = auth()->user();

        $deployableTypes = $user->isCivilian()
            ? ['ward']
            : (($user->isGhostHunter() || $user->isAdmin()) ? ['ward', 'anchor', 'trap', 'scanner'] : []);

        $owned = Inventory::with('gear')
            ->where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->get()
            ->filter(fn ($item) => $item->gear && in_array($item->gear->type, $deployableTypes, true))
            ->values();

        $batteries = Inventory::with('gear')
            ->where('user_id', $user->id)
            ->where('quantity', '>', 0)
            ->whereHas('gear', fn ($q) => $q->where('type', 'battery'))
            ->get();

        $devices = ProtectionDevice::with('gear')
            ->where('user_id', $user->id)
            ->latest()
            ->get()
            ->map(function ($device) {
                $device->battery_percent = $device->batteryPercent();
                $device->remaining_minutes = $device->remainingMinutes();
                $device->powered = $device->isPowered();
                return $device;
            });

        return view('protection.index', compact('user', 'owned', 'batteries', 'devices'));
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
        $allowedTypes = $user->isCivilian()
            ? ['ward']
            : (($user->isGhostHunter() || $user->isAdmin()) ? ['ward', 'anchor', 'trap', 'scanner'] : []);

        if (! in_array($gear->type, $allowedTypes, true)) {
            return back()->withErrors(['gear_id' => 'Your role cannot deploy this equipment.'])->withInput();
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

            // A physical item can only be deployed once per owned quantity.
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
                'label' => $data['label'] ?: $gear->name,
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

        return redirect('/protection')->with('success', $gear->name . ' deployed. Its protection radius is ' . $device->radius_meters . ' metres and its battery lasts up to 24 hours.');
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
            return redirect('/protection')->withErrors([
                'battery' => 'You need a Standard Spectral Battery. Buy one from the Gear Shop first.',
            ]);
        }

        return redirect('/protection')->with('success', 'Battery replaced. This device has up to 24 hours of power.');
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

            $lockedUser->decrement('ecto', $cost);
            $device->forceFill([
                'battery_minutes' => 1440,
                'activated_at' => now(),
                'is_active' => true,
            ])->save();

            return true;
        });

        if (! $recharged) {
            return redirect('/protection')->withErrors([
                'battery' => 'You need 10 Ecto to recharge this device, or you can buy a replacement battery from the Gear Shop.',
            ]);
        }

        return redirect('/protection')->with('success', 'Battery recharged for 10 Ecto. This device has up to 24 hours of power.');
    }

    public function deactivate(Request $request, $id)
    {
        $device = ProtectionDevice::where('user_id', $request->user()->id)->findOrFail($id);
        $device->update(['is_active' => false]);

        return redirect('/protection')->with('success', 'Device switched off.');
    }
}
