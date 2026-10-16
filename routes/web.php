<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\GearController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\LoadoutController;
use App\Http\Controllers\MarketplaceController;
use App\Http\Controllers\PurchaseController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\TradeProposalController;
use App\Http\Controllers\WantedPostController;
use App\Http\Controllers\MapController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\RoleApplicationController;
use App\Http\Controllers\AchievementController;
use App\Http\Controllers\LicenseController;
use App\Http\Controllers\MessageController;


// ─── Public routes (no auth required) ──────────
Route::get('/', [HomeController::class, 'index']);


// ─── Admin-only (must come FIRST) ──────────────
Route::middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/gears/create', [GearController::class, 'create']);
    Route::post('/gears',       [GearController::class, 'store']);

    Route::get('/admin',                         [AdminController::class, 'home']);
    Route::get('/admin/settings',                [AdminController::class, 'settings']);
    Route::post('/admin/settings',               [AdminController::class, 'updateSettings']);
    Route::post('/admin/achievements/{id}/toggle',    [AdminController::class, 'toggleAchievement']);
    Route::post('/admin/achievements/{id}/threshold', [AdminController::class, 'updateAchievement']);

    Route::get('/admin/applications',                [AdminController::class, 'applications']);
    Route::get('/admin/applications/{id}',           [AdminController::class, 'showApplication']);
    Route::post('/admin/applications/{id}/approve',  [AdminController::class, 'approveApplication']);
    Route::post('/admin/applications/{id}/reject',   [AdminController::class, 'rejectApplication']);

    Route::get('/admin/users',         [AdminController::class, 'users']);
    Route::get('/admin/users/{id}',    [AdminController::class, 'showUser']);
    Route::post('/admin/users/{id}',   [AdminController::class, 'updateUser']);

    // Redirect admins from user-only pages
    Route::get('/achievements',        fn () => redirect('/admin'));
    Route::get('/loadout',             fn () => redirect('/admin'));
    Route::get('/drain',               fn () => redirect('/admin'));
    Route::get('/apply/merchant',      fn () => redirect('/admin'));
    Route::get('/apply/hunter',        fn () => redirect('/admin'));
    Route::get('/applications/status', fn () => redirect('/admin'));
});


// ─── Logged-in users ───────────────────────────
Route::middleware('auth')->group(function () {

    // ─── Gear Shop ─────────────────────────────
    Route::get('/gears',                       [GearController::class, 'index']);
    Route::get('/gears/{id}',                  [GearController::class, 'show']);
    Route::post('/gears/{id}/buy',             [GearController::class, 'buy']);

    // ─── Inventory ─────────────────────────────
    Route::get('/inventory',                   [InventoryController::class, 'index']);

    // ─── Loadout ───────────────────────────────
    Route::get('/loadout',                     [LoadoutController::class, 'index']);
    Route::post('/loadout/{gearId}/equip',     [LoadoutController::class, 'equip']);
    Route::post('/loadout/{gearId}/unequip',   [LoadoutController::class, 'unequip']);

    // ─── Marketplace ───────────────────────────
    Route::get('/marketplace',                 [MarketplaceController::class, 'index']);
    Route::get('/marketplace/mine',            [MarketplaceController::class, 'mine']);
    Route::get('/marketplace/create',          [MarketplaceController::class, 'create']);
    Route::post('/marketplace',                [MarketplaceController::class, 'store']);

    // ─── Wanted Ads (before dynamic /marketplace/{id}) ──
    Route::get('/marketplace/wanted/create',        [WantedPostController::class, 'create']);
    Route::post('/marketplace/wanted',              [WantedPostController::class, 'store']);
    Route::get('/marketplace/wanted/mine',          [WantedPostController::class, 'mine']);
    Route::get('/marketplace/wanted/{id}/respond',  [WantedPostController::class, 'respond']);
    Route::post('/marketplace/wanted/{id}/respond', [WantedPostController::class, 'storeResponse']);
    Route::post('/marketplace/wanted/responses/{id}/accept', [WantedPostController::class, 'acceptResponse']);
    Route::post('/marketplace/wanted/responses/{id}/reject', [WantedPostController::class, 'rejectResponse']);

    // ─── Dynamic marketplace routes ────────────
    Route::post('/marketplace/{id}/buy',       [MarketplaceController::class, 'buy']);
    Route::post('/marketplace/{id}/cancel',    [MarketplaceController::class, 'cancel']);

    // ─── Trade proposals ───────────────────────
    Route::get('/marketplace/proposals',              [TradeProposalController::class, 'inbox']);
    Route::get('/marketplace/my-proposals',           [TradeProposalController::class, 'mine']);
    Route::get('/marketplace/{id}/propose-trade',     [TradeProposalController::class, 'create']);
    Route::post('/marketplace/{id}/propose-trade',    [TradeProposalController::class, 'store']);
    Route::post('/marketplace/proposals/{id}/accept', [TradeProposalController::class, 'accept']);
    Route::post('/marketplace/proposals/{id}/reject', [TradeProposalController::class, 'reject']);

    // ─── Messages ──────────────────────────────
    Route::get('/messages',                          [MessageController::class, 'inbox']);
    Route::get('/messages/support',                  [MessageController::class, 'contactSupport']);
    Route::get('/messages/start/{userId}',           [MessageController::class, 'startWith']);
    Route::get('/messages/{id}',                     [MessageController::class, 'show']);
    Route::post('/messages/{id}',                    [MessageController::class, 'send']);
    Route::get('/messages/{id}/poll',                [MessageController::class, 'poll']);

    // ─── Notifications ─────────────────────────
    Route::get('/notifications',              [NotificationController::class, 'index']);
    Route::post('/notifications/{id}/read',   [NotificationController::class, 'markRead']);
    Route::post('/notifications/read-all',    [NotificationController::class, 'markAllRead']);
    Route::post('/notifications/clear',       [NotificationController::class, 'clearAll']);

    // ─── Purchases ─────────────────────────────
    Route::get('/purchases',                  [PurchaseController::class, 'index']);

    // ─── Profile ───────────────────────────────
    Route::get('/profile',                    [ProfileController::class, 'index']);
    Route::get('/profile/location',           [ProfileController::class, 'locationForm']);
    Route::post('/profile/location',          [ProfileController::class, 'saveLocation']);
    Route::get('/profile/license',            [LicenseController::class, 'show']);

    // ─── Map ───────────────────────────────────
    Route::get('/map', [MapController::class, 'index']);

    // ─── Drain Energy (placeholder) ────────────
    Route::view('/drain', 'placeholders.drain');

    // ─── Role Applications ─────────────────────
    Route::get('/apply/merchant',  [RoleApplicationController::class, 'showMerchantForm']);
    Route::post('/apply/merchant', [RoleApplicationController::class, 'submitMerchant']);
    Route::get('/apply/hunter',    [RoleApplicationController::class, 'showHunterForm']);
    Route::post('/apply/hunter',   [RoleApplicationController::class, 'submitHunter']);
    Route::get('/applications/status', [RoleApplicationController::class, 'status']);

    // ─── Achievements ──────────────────────────
    Route::get('/achievements', [AchievementController::class, 'index']);
});

require __DIR__.'/auth.php';