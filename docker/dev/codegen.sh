#!/usr/bin/env bash
set -euo pipefail

stamp=/tmp/codegen.stamp
ready=/tmp/codegen.ready

if [ -z "${APP_KEY:-}" ]; then
    APP_KEY="base64:$(head -c 32 /dev/urandom | base64)"
    export APP_KEY
fi

generate() {
    touch "$stamp"
    php -d memory_limit=2G artisan wayfinder:generate || echo "failed: php -d memory_limit=2G artisan wayfinder:generate" >&2
    php artisan typescript:transform || echo "failed: php artisan typescript:transform" >&2
}

changed_since_last_run() {
    [ -n "$(find routes app -name '*.php' \( -path 'routes/*' -o -path '*/Http/*' -o -path 'app/Data/*' -o -path 'app/Enums/*' -o -path 'app/Models/*' \) -newer "$stamp" -print -quit 2>/dev/null)" ]
}

generate
touch "$ready"

while sleep 2; do
    if changed_since_last_run; then
        generate
    fi
done
