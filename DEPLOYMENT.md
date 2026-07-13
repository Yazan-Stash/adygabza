# Deployment

This app deploys as a single FrankenPHP image running Laravel Octane worker mode. Production uses fix-forward releases only: if a deploy fails, ship a new patch tag and deploy that tag.

## Image

Images are published to GHCR from GitHub Actions with immutable version tags.

```txt
ghcr.io/OWNER/adygabza:VERSION
```

Replace `OWNER` and `VERSION` in `compose.production.yaml` before deploying.

## Host Setup

The Debian server runs rootful Docker. The container process runs as UID/GID `10001:10001`, so writable bind mounts must be owned by that numeric user.

Create the host directory layout:

```bash
mkdir -p /opt/adygabza/database /opt/adygabza/storage/app /opt/adygabza/storage/logs
touch /opt/adygabza/database/database.sqlite

chown -R 10001:10001 /opt/adygabza/database /opt/adygabza/storage
chmod -R u+rwX,g+rwX /opt/adygabza/database /opt/adygabza/storage
```

Persisted paths:

```txt
/opt/adygabza/database
/opt/adygabza/storage/app
/opt/adygabza/storage/logs
```

Do not persist `storage/framework` or `bootstrap/cache`; runtime cache files are regenerated after environment variables are loaded.

## Environment

Create `/opt/adygabza/.env` manually on the server and keep it out of git:

```bash
chmod 600 /opt/adygabza/.env
```

SQLite-first database settings:

```env
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-domain.example

DB_CONNECTION=sqlite
DB_DATABASE=/app/storage/database/database.sqlite

SESSION_DRIVER=database
CACHE_STORE=database
QUEUE_CONNECTION=database

LOG_CHANNEL=stack
LOG_STACK=single
LOG_LEVEL=info

OCTANE_SERVER=frankenphp
OCTANE_HOST=0.0.0.0
OCTANE_PORT=8080
OCTANE_WORKERS=auto
OCTANE_MAX_REQUESTS=500
OCTANE_HTTPS=false
```

For object storage, configure the S3-compatible variables and use:

```env
FILESYSTEM_DISK=s3
```

For local upload fallback, use:

```env
FILESYSTEM_DISK=public
```

## Host Caddy

Terminate TLS in host Caddy and proxy to the app container on localhost port `8080`:

```caddyfile
your-domain.example {
    reverse_proxy 127.0.0.1:8080
}
```

## Deploy

Deployments are pull, migrate, restart:

```bash
cd /opt/adygabza
docker compose -f compose.production.yaml pull
docker compose -f compose.production.yaml run --rm app migrate
docker compose -f compose.production.yaml up -d
curl -fsS https://your-domain.example/up
```

If production breaks, do not roll back. Commit a fix, tag a new patch version, let GHCR publish it, then repeat the deploy steps.

## Backups

Back up these paths with host cron or server backup tooling:

```txt
/opt/adygabza/database/database.sqlite
/opt/adygabza/storage/app
/opt/adygabza/storage/logs
```

Run a backup before applying reviewed migrations.
