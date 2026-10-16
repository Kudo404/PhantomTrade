<?php

namespace App\Http\Controllers;

use App\Models\Gear;
use App\Models\Inventory;
use App\Models\Loadout;
use App\Models\User;
use App\Services\AchievementService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoadoutController extends Controller
{
    /**
     * Show the Spectral Gear Customizer & Resource Tracker.
     */
    public function index()
    {
        $user   = auth()->user();
        $userId = $user->id;

        // Auto-cleanup: remove any equipped gear the user no longer owns
        $ownedGearIds = Inventory::where('user_id', $userId)
            ->where('quantity', '>', 0)
            ->pluck('gear_id')
            ->toArray();

        Loadout::where('user_id', $userId)
            ->whereNotIn('gear_id', $ownedGearIds)
            ->delete();

        // What I've equipped with gear details
        $equipped = Loadout::with('gear')
            ->where('user_id', $userId)
            ->get();

        // Tactical slot breakdown
        $slots = [
            'ward' => [
                'label'       => 'Spirit Ward',
                'description' => 'Absorbs spectral life-force drain & corruption.',
                'icon'        => 'shield',
                'equipped'    => $equipped->first(fn ($l) => $l->slot === 'ward' || ($l->gear && $l->gear->type === 'ward')),
            ],
            'anchor' => [
                'label'       => 'Soul-Anchor',
                'description' => 'Prevents astral detachment & stabilizes soul tether.',
                'icon'        => 'anchor',
                'equipped'    => $equipped->first(fn ($l) => $l->slot === 'anchor' || ($l->gear && $l->gear->type === 'anchor')),
            ],
            'trap' => [
                'label'       => 'Containment Unit',
                'description' => 'Captures and seals neutralized ectoplasmic entities.',
                'icon'        => 'target',
                'equipped'    => $equipped->first(fn ($l) => $l->slot === 'trap' || ($l->gear && $l->gear->type === 'trap')),
            ],
            'scanner' => [
                'label'       => 'Spectral Gadget',
                'description' => 'EMF frequency detector & occult entity tracking.',
                'icon'        => 'scan-line',
                'equipped'    => $equipped->first(fn ($l) => $l->slot === 'scanner' || ($l->gear && $l->gear->type === 'scanner')),
            ],
        ];

        $equippedGearIds = $equipped->pluck('gear_id')->toArray();

        // Owned gear available to equip
        $myItems = Inventory::with('gear')
            ->where('user_id', $userId)
            ->where('quantity', '>', 0)
            ->get();

        // Live Telemetry from User Model
        $telemetry = $user->spectralRigSummary();

        // Totals
        $totalEctoValue = $equipped->sum(fn ($l) => $l->gear ? $l->gear->ecto_cost : 0);
        $totalBaseEnergy = $equipped->sum(fn ($l) => $l->gear ? $l->gear->energy_cost : 0);
        $totalEffectiveEnergy = $equipped->sum(fn ($l) => $l->effective_energy_drain);

        return view('loadout.index', compact(
            'myItems',
            'equipped',
            'equippedGearIds',
            'slots',
            'telemetry',
            'totalEctoValue',
            'totalBaseEnergy',
            'totalEffectiveEnergy'
        ));
    }

    /**
     * Equip gear into its appropriate tactical slot.
     */
    public function equip($gearId)
    {
        $userId = auth()->id();

        // Make sure user owns it
        $owns = Inventory::where('user_id', $userId)
            ->where('gear_id', $gearId)
            ->where('quantity', '>', 0)
            ->exists();

        if (! $owns) {
            return back()->withErrors(['loadout' => 'You do not own that gear.']);
        }

        $gear = Gear::findOrFail($gearId);
        $slotType = in_array($gear->type, ['ward', 'anchor', 'trap', 'scanner']) ? $gear->type : 'gadget';

        // Swap out existing gear in the same slot to prevent duplicate slot abuse
        Loadout::where('user_id', $userId)
            ->where(function ($q) use ($slotType, $gearId) {
                $q->where('slot', $slotType)
                  ->orWhere('gear_id', $gearId);
            })
            ->delete();

        Loadout::create([
            'user_id'            => $userId,
            'gear_id'            => $gearId,
            'slot'               => $slotType,
            'tuning_mode'        => 'balanced',
            'frequency_khz'      => 440,
            'ward_reinforcement' => 0,
            'custom_name'        => null,
        ]);

        return redirect('/loadout')->with('success', "✅ Equipped {$gear->name} into your " . ucfirst($slotType) . " slot!");
    }

    /**
     * Calibrate / Tune an equipped gear item.
     */
    public function tune(Request $request, $gearId)
    {
        $userId = auth()->id();

        $loadout = Loadout::with('gear')
            ->where('user_id', $userId)
            ->where('gear_id', $gearId)
            ->firstOrFail();

        $validated = $request->validate([
            'tuning_mode'        => 'required|in:balanced,overclocked,shielded,stealth',
            'frequency_khz'      => 'required|integer|min:100|max:1000',
            'ward_reinforcement' => 'nullable|integer|min:0|max:50',
            'custom_name'        => 'nullable|string|max:80',
        ]);

        $loadout->update([
            'tuning_mode'        => $validated['tuning_mode'],
            'frequency_khz'      => $validated['frequency_khz'],
            'ward_reinforcement' => $validated['ward_reinforcement'] ?? 0,
            'custom_name'        => $validated['custom_name'] ?: null,
        ]);

        return redirect('/loadout')->with('success', "⚙️ Custom tuning saved for {$loadout->display_name} ({$loadout->tuning_mode_label})!");
    }

    /**
     * Unequip a single gear.
     */
    public function unequip($gearId)
    {
        Loadout::where('user_id', auth()->id())
            ->where('gear_id', $gearId)
            ->delete();

        return redirect('/loadout')->with('success', 'Gear unequipped from tactical rig.');
    }

    /**
     * Unequip all gear.
     */
    public function resetAll()
    {
        Loadout::where('user_id', auth()->id())->delete();

        return redirect('/loadout')->with('success', 'All tactical slots cleared.');
    }

    /**
     * Field Haunt & Drain Simulation Chamber (Scenario 3).
     */
    public function drainSim()
    {
        $user = auth()->user();
        $equipped = Loadout::with('gear')->where('user_id', $user->id)->get();
        $telemetry = $user->spectralRigSummary();

        // Spectral Encounter Classes
        $entities = [
            [
                'id'          => 'poltergeist',
                'name'        => 'Kinetic Poltergeist',
                'class'       => 'Class I',
                'freq'        => 280,
                'danger'      => 'Moderate',
                'base_drain'  => 22,
                'ecto_reward' => 20,
                'icon'        => 'flame',
                'desc'        => 'Violent spectral manifestation throwing physical objects and draining thermal energy.',
            ],
            [
                'id'          => 'banshee',
                'name'        => 'Acoustic Banshee',
                'class'       => 'Class II',
                'freq'        => 480,
                'danger'      => 'High',
                'base_drain'  => 36,
                'ecto_reward' => 40,
                'icon'        => 'volume-2',
                'desc'        => 'Sonic scream ruptures unshielded spirit wards and rapidly destabilizes soul tethers.',
            ],
            [
                'id'          => 'shadow_fiend',
                'name'        => 'Astral Shadow Fiend',
                'class'       => 'Class III',
                'freq'        => 680,
                'danger'      => 'Severe',
                'base_drain'  => 50,
                'ecto_reward' => 65,
                'icon'        => 'ghost',
                'desc'        => 'Vampiric presence that leeches life force directly from mediums lacking a Soul-Anchor.',
            ],
            [
                'id'          => 'ancient_wraith',
                'name'        => 'Cataclysmic Wraith',
                'class'       => 'Class IV',
                'freq'        => 880,
                'danger'      => 'Extreme',
                'base_drain'  => 68,
                'ecto_reward' => 100,
                'icon'        => 'skull',
                'desc'        => 'Corrosive ethereal apparition. Demands an Overclocked Containment Unit and reinforced Ward.',
            ],
        ];

        return view('placeholders.drain', compact('user', 'equipped', 'telemetry', 'entities'));
    }

    /**
     * Execute a simulated spectral drain test.
     */
    public function executeDrain(Request $request)
    {
        $user = auth()->user();

        $validated = $request->validate([
            'entity_id' => 'required|in:poltergeist,banshee,shadow_fiend,ancient_wraith',
        ]);

        $entities = [
            'poltergeist'   => ['name' => 'Kinetic Poltergeist', 'freq' => 280, 'base_drain' => 22, 'reward' => 20],
            'banshee'       => ['name' => 'Acoustic Banshee', 'freq' => 480, 'base_drain' => 36, 'reward' => 40],
            'shadow_fiend'  => ['name' => 'Astral Shadow Fiend', 'freq' => 680, 'base_drain' => 50, 'reward' => 65],
            'ancient_wraith'=> ['name' => 'Cataclysmic Wraith', 'freq' => 880, 'base_drain' => 68, 'reward' => 100],
        ];

        $target = $entities[$validated['entity_id']];
        $telemetry = $user->spectralRigSummary();

        // Telemetry Evaluation
        $wardAbsorbed = 0;
        $actualDrain = $target['base_drain'];

        if ($telemetry['has_ward']) {
            $wardAbsorbed = (int) round(($target['base_drain'] * $telemetry['ward_absorption']) / 100);
            $actualDrain = max(4, $target['base_drain'] - $wardAbsorbed);
        }

        $anchorHeld = $telemetry['has_anchor'];
        $instabilityDamage = 0;
        if (! $anchorHeld) {
            $instabilityDamage = 15;
            $actualDrain += $instabilityDamage;
        }

        // Frequency Resonance Check (closest tuned gear)
        $equipped = Loadout::with('gear')->where('user_id', $user->id)->get();
        $scanner = $equipped->first(fn ($l) => $l->slot === 'scanner' || ($l->gear && $l->gear->type === 'scanner'));
        $freqDifference = $scanner ? abs($scanner->frequency_khz - $target['freq']) : 500;
        $frequencyMatched = $freqDifference <= 120;

        $hasTrap = $telemetry['has_trap'];
        $captured = $hasTrap && $frequencyMatched && $telemetry['stability'] >= 40;

        $ectoEarned = 0;
        if ($captured) {
            $ectoEarned = $target['reward'];
            $user->addEcto($ectoEarned);

            // Trigger potential achievements
            AchievementService::checkAndUnlock($user);

            notify(
                $user->id,
                'Spectral Entity Contained!',
                "Your custom rig captured a {$target['name']}! Harvested ₱ {$ectoEarned} Ecto.",
                '/drain',
                'zap'
            );
        } else {
            notify(
                $user->id,
                'Drain Chamber Exposure',
                "Encountered {$target['name']}. Suffered {$actualDrain} EP drain. Ward absorbed {$wardAbsorbed} EP.",
                '/drain',
                'battery-charging'
            );
        }

        $result = [
            'captured'           => $captured,
            'entity_name'        => $target['name'],
            'base_drain'         => $target['base_drain'],
            'ward_absorbed'      => $wardAbsorbed,
            'actual_drain'       => $actualDrain,
            'anchor_held'        => $anchorHeld,
            'frequency_matched'  => $frequencyMatched,
            'ecto_earned'        => $ectoEarned,
        ];

        return redirect('/drain')->with('simulation_result', $result);
    }
}