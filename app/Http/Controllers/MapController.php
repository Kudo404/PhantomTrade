<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\WantedPost;

class MapController extends Controller
{
    public function index()
    {
        // Users with a saved location
        $users = User::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get([
                'id',
                'name',
                'role',
                'location_name',
                'latitude',
                'longitude',
            ]);

        // Wanted ads where the poster has a location, and the ad is still open
        $wantedPosts = WantedPost::with('gear', 'user')
            ->where('status', 'open')
            ->whereHas('user', function ($q) {
                $q->whereNotNull('latitude')->whereNotNull('longitude');
            })
            ->latest()
            ->get();

        // Build a simple array for JS (avoids serialization surprises)
        $userPins = $users->map(function ($u) {
            return [
                'id'            => $u->id,
                'name'          => $u->name,
                'role'          => $u->role,
                'role_label'    => $u->roleLabel(),
                'location_name' => $u->locationLabel(),
                'lat'           => (float) $u->latitude,
                'lng'           => (float) $u->longitude,
            ];
        })->values();

        $wantedPins = $wantedPosts->map(function ($w) {
            return [
                'id'             => $w->id,
                'gear_name'      => $w->gear->name,
                'quantity'       => $w->quantity,
                'price_per_unit' => $w->price_per_unit,
                'poster_name'    => $w->user->name,
                'poster_role'    => $w->user->roleLabel(),
                'poster_role_key'   => $w->user->role,
                'note'           => $w->note,
                'lat'            => (float) $w->user->latitude,
                'lng'            => (float) $w->user->longitude,
                'url'            => '/marketplace',
            ];
        })->values();

        return view('map.index', compact('userPins', 'wantedPins'));
    }
}