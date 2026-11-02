#!/usr/bin/env bash
set -euo pipefail

HOST="${DB_HOST:-127.0.0.1}"
PORT="${DB_PORT:-5432}"
USER="${DB_USERNAME:-root}"
NAME="${DB_DATABASE:-laravel_test}"

run_psql() {
    local database="$1"
    shift
    PGOPTIONS="-c client_min_messages=warning" psql -h "$HOST" -p "$PORT" -U "$USER" -d "$database" -v ON_ERROR_STOP=1 -q "$@"
}

for db in "$NAME" "${NAME}_test_1" "${NAME}_test_2" "${NAME}_test_3" "${NAME}_test_4"; do
    if [ "$(run_psql postgres -tAc "SELECT 1 FROM pg_database WHERE datname = '$db'")" != "1" ]; then
        run_psql postgres -c "CREATE DATABASE \"$db\""
    fi
    echo "$db ready"
done
