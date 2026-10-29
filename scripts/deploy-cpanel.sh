#!/usr/bin/env bash
set -Eeuo pipefail
umask 077

LIVE=/home/mwanafun/mwanafunzi-release/Mwanafunzi-investor-fresh
WEB=/home/mwanafun/public_html
BASE=/home/mwanafun/deploy
PHP=/usr/local/bin/php
ID="${1:?Release ID required}"

[[ "$ID" =~ ^[a-f0-9]+-[0-9]+-[0-9]+$ ]] || exit 1

IN="$BASE/incoming/$ID"
BACKUP="$BASE/backups/$ID"

mkdir -p "$BASE/backups"
exec 9>"$BASE/deployment.lock"
flock -n 9 || {
    echo "Another deployment is running."
    exit 1
}

test -f "$LIVE/.env"
test -f "$LIVE/artisan"
test -f "$WEB/index.php"
test -s "$IN/release.tar.gz"
command -v curl >/dev/null

# Keep generated release artifacts from exhausting the cPanel account quota.
# The active release and the newest rollback backup are preserved; persistent
# uploads, databases and application storage live outside these directories.
prune_release_artifacts() {
    find "$BASE/incoming" -mindepth 1 -maxdepth 1 -type d ! -path "$IN" -exec rm -rf -- {} +

    local newest_backup=""
    newest_backup="$(find "$BASE/backups" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' | sort -nr | head -n 1 | cut -d' ' -f2- || true)"
    while IFS= read -r backup; do
        [[ -z "$backup" || "$backup" == "$newest_backup" ]] && continue
        rm -rf -- "$backup"
    done < <(find "$BASE/backups" -mindepth 1 -maxdepth 1 -type d -printf '%T@ %p\n' | sort -nr | cut -d' ' -f2-)
}

prune_release_artifacts

# Do not interfere with pre-existing maintenance mode.
if [[ -e "$LIVE/storage/framework/down" ||
      -e "$LIVE/storage/framework/maintenance.php" ]]; then
    echo "Website is already in maintenance mode. Deployment stopped."
    exit 1
fi

mkdir -p "$IN/app"
tar -xzf "$IN/release.tar.gz" -C "$IN/app"
cd "$IN/app"

test -s vendor/autoload.php
test -s public/build/manifest.json

# Use production settings for preflight checks, without replacing them.
ln -s "$LIVE/.env" .env
mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache

"$PHP" artisan package:discover --no-ansi

# Schema changes require a separately backed-up migration procedure.
"$PHP" artisan migrate:status --no-ansi > "$IN/migrations.txt"
if grep -Eq '(^|[[:space:]])Pending([[:space:]]|$)' "$IN/migrations.txt"; then
    cat "$IN/migrations.txt"
    echo "Pending database migrations found. Live files were not changed."
    exit 1
fi

# Private code backups. Database and persistent storage are not changed.
mkdir -p "$BACKUP/app" "$BACKUP/web"

rsync -a \
    --exclude='/.env*' \
    --exclude='/.git/' \
    --exclude='/storage/' \
    "$LIVE/" "$BACKUP/app/"

rsync -a "$WEB/" "$BACKUP/web/"

# Root application sync preserves persistent data and public uploads.
APP_FILTERS=(
    --exclude='/.env*'
    --exclude='/.git/'
    --exclude='/storage/'
    --exclude='/public/'
    --exclude='/database/*.sqlite*'
    --exclude='/.user.ini'
)

# Public sync preserves hosting configuration and runtime uploads.
# No --delete is used for public directories.
PUBLIC_FILTERS=(
    --exclude='/index.php'
    --exclude='/.htaccess'
    --exclude='/.user.ini'
    --exclude='/.well-known/'
    --exclude='/storage'
    --exclude='/uploads'
    --exclude='/hot'
)

STARTED=0

rollback() {
    result=$?
    trap - EXIT
    if (( result != 0 && STARTED == 1 )); then
        echo "Deployment failed. Attempting code rollback."
        set +e

        rsync -a --delete "${APP_FILTERS[@]}" \
            "$BACKUP/app/" "$LIVE/"
        app_result=$?

        rsync -a "${PUBLIC_FILTERS[@]}" \
            "$BACKUP/app/public/" "$LIVE/public/"
        public_result=$?

        rsync -a "${PUBLIC_FILTERS[@]}" \
            "$BACKUP/web/" "$WEB/"
        web_result=$?

        if (( app_result == 0 && public_result == 0 && web_result == 0 )); then
            cd "$LIVE"
            "$PHP" artisan up
            echo "Previous code restored. Check all three websites."
        else
            echo "Rollback incomplete. Maintenance mode retained."
            echo "Backup: $BACKUP"
        fi
    fi
    exit "$result"
}
trap rollback EXIT

cd "$LIVE"
STARTED=1
"$PHP" artisan down --render="errors::503" --retry=30

rsync -a --delete "${APP_FILTERS[@]}" \
    --exclude='/bootstrap/cache/' \
    "$IN/app/" "$LIVE/"

# Update admin/client public assets and the main website assets.
rsync -a "${PUBLIC_FILTERS[@]}" \
    "$IN/app/public/" "$LIVE/public/"

rsync -a "${PUBLIC_FILTERS[@]}" \
    "$IN/app/public/" "$WEB/"

# Remove only generated framework caches, then rebuild on the server.
find "$LIVE/bootstrap/cache" -maxdepth 1 -type f -name '*.php' -delete

"$PHP" artisan package:discover --no-ansi
"$PHP" artisan config:cache
"$PHP" artisan route:cache
"$PHP" artisan view:cache
"$PHP" artisan up

for url in \
    "https://mwanafunziinvestor.com/" \
    "https://client.mwanafunziinvestor.com/login" \
    "https://admin.mwanafunziinvestor.com/login"
do
    curl --fail --silent --show-error --location \
        --max-time 30 "$url" -o /dev/null
done

"$PHP" artisan queue:restart

STARTED=0
printf '%s\n' "$ID" > "$BASE/last-successful-release"
rm -rf -- "$IN"
echo "Deployment succeeded: $ID"
echo "Code backup: $BACKUP"
