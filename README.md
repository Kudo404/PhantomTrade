# PhantomTrade

PhantomTrade is a Laravel-powered supernatural gear marketplace and spectral field simulator. Mediums and ghost hunters purchase equipment with Ecto, trade with other players, customize their rig, and test it against spectral encounters.

## Scenario features

- **Gear marketplace:** browse and buy scanners, spirit wards, soul-anchors, containment traps, and other gear.
- **Player economy:** inventory, listings, Ecto purchases, trade proposals, and wanted posts.
- **Gear customization:** equip tactical slots, set tuning modes and frequencies, reinforce wards, and name loadout items.
- **Spectral encounters:** test a configured rig against Poltergeists, Banshees, Shadow Fiends, and Ancient Wraiths.
- **Persistent resources:** each encounter consumes stored rig energy and life-force. A ward reduces exposure; a soul-anchor reduces life-force damage.
- **Recovery:** spend 10 Ecto to restore energy and life-force to 100. Encounters are rejected atomically when either resource cannot cover its cost.
- **Admin tools:** manage gear and other administrative functions.

## Local setup

Requirements: PHP 8.2+, Composer, Node.js/npm, and a database supported by Laravel.

1. Install PHP dependencies:
   ```bash
   composer install
   ```
2. Create local environment settings and app key:
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```
3. Configure the database in `.env`, then run migrations:
   ```bash
   php artisan migrate
   ```
4. Install/build frontend assets:
   ```bash
   npm install
   npm run build
   ```
5. Link public file storage:
   ```bash
   php artisan storage:link
   ```
6. Start the local server:
   ```bash
   php artisan serve
   ```

## Tests

Run the automated test suite with:

```bash
php artisan test
```

Spectral resource feature tests cover encounter costs, energy and life-force lockouts, paid recovery, and invalid encounter input.

## Railway deployment notes

- The container startup script runs Laravel migrations unless `RAILPACK_SKIP_MIGRATIONS=true`. Confirm the production database is backed up and reachable before deployment.
- The new spectral-resource migration adds `energy` and `life_force` columns with defaults of 100, so existing users receive initial reserves without manual data edits.
- Gear uploads use Laravel's public storage disk. The `storage:link` command only creates a symlink; it does **not** make uploaded files persistent across container replacement. Mount persistent storage at the application's `storage/app/public` directory or move uploads to persistent object storage, then verify image URLs after redeploying.
- Do not commit production secrets or `.env` values.

## Stack

- Laravel 9 / PHP 8.2+
- Blade views
- Vite frontend assets
- Eloquent ORM and Laravel migrations
