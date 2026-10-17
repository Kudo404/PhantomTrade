<?php

namespace App\Http\Controllers;

use App\Models\Inventory;
use App\Models\ProtectionDevice;
use App\Models\User;
use App\Models\WantedPost;

class MapController extends Controller
{
    public function index()
    {
        $users = User::whereNotNull('latitude')->whereNotNull('longitude')->get(['id','name','role','location_name','latitude','longitude']);
        $wantedPosts = WantedPost::with('gear', 'user')->where('status','open')->whereHas('user', fn ($q) => $q->whereNotNull('latitude')->whereNotNull('longitude'))->latest()->get();
        $userPins = $users->map(fn ($u) => ['id'=>$u->id,'name'=>$u->name,'role'=>$u->role,'role_label'=>$u->roleLabel(),'location_name'=>$u->locationLabel(),'lat'=>(float)$u->latitude,'lng'=>(float)$u->longitude])->values();
        $wantedPins = $wantedPosts->map(fn ($w) => ['id'=>$w->id,'gear_name'=>$w->gear->name,'quantity'=>$w->quantity,'price_per_unit'=>$w->price_per_unit,'poster_name'=>$w->user->name,'poster_role'=>$w->user->roleLabel(),'poster_role_key'=>$w->user->role,'note'=>$w->note,'lat'=>(float)$w->user->latitude,'lng'=>(float)$w->user->longitude,'url'=>'/marketplace'])->values();
        $owned = Inventory::with('gear')->where('user_id', auth()->id())->where('quantity','>',0)->whereHas('gear', fn ($q) => $q->whereIn('type',['ward','anchor','trap','scanner']))->get();
        $equipmentPins = ProtectionDevice::with(['gear','user'])->latest()->get()->map(fn ($d) => ['id'=>$d->id,'name'=>$d->label ?: $d->gear->name,'type'=>$d->gear->type,'owner'=>$d->user->name,'lat'=>(float)$d->latitude,'lng'=>(float)$d->longitude,'radius'=>(int)$d->radius_meters,'powered'=>(bool)$d->isPowered(),'battery'=>(int)$d->batteryPercent()])->values();
        return view('map.index', compact('userPins','wantedPins','owned','equipmentPins'));
    }
}
