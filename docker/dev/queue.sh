#!/usr/bin/env bash
set -uo pipefail

stamp=/tmp/queue.stamp
watched=(app bootstrap/app.php bootstrap/providers.php config database lang resources/views routes .env composer.lock)
worker=""

start_worker() {
    touch "$stamp"
    php artisan queue:work "$@" &
    worker=$!
}

stop_worker() {
    kill -TERM "$worker" 2>/dev/null || true
    wait "$worker" 2>/dev/null || true
}

code_changed() {
    [ -n "$(find "${watched[@]}" -newer "$stamp" -type f -print -quit 2>/dev/null)" ]
}

trap 'stop_worker; exit 0' TERM INT

start_worker "$@"

while sleep 2; do
    if code_changed; then
        echo "Code changed, restarting the queue worker."
        stop_worker
        start_worker "$@"
    elif ! kill -0 "$worker" 2>/dev/null; then
        wait "$worker" 2>/dev/null || true
        echo "The queue worker exited, starting it again."
        start_worker "$@"
    fi
done
