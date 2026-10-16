#!/bin/bash
set -e

# Run migrations if database connection is available
if [ "$RAILPACK_SKIP_MIGRATIONS" != "true" ] && { [ -n "$DB_HOST" ] || [ -n "$DATABASE_URL" ]; }; then
  echo "Checking database connection and running migrations..."
  php artisan migrate --force || echo "Migrations skipped or failed, continuing container startup..."
fi

# Ensure storage symlink exists
php artisan storage:link || true

# Refresh optimization caches
php artisan optimize:clear
php artisan optimize || true

echo "Starting FrankenPHP application server on port ${PORT:-80}..."
exec docker-php-entrypoint --config /Caddyfile --adapter caddyfile 2>&1
