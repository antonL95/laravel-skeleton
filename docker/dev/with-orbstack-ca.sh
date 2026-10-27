#!/usr/bin/env bash
set -euo pipefail

ca_bundle=/usr/local/share/ca-certificates/orbstack-root.crt
nss_db="$HOME/.pki/nssdb"

if [ -f "$ca_bundle" ] && command -v certutil >/dev/null 2>&1; then
    mkdir -p "$nss_db"
    if [ ! -f "$nss_db/cert9.db" ]; then
        certutil -d "sql:$nss_db" -N --empty-password
    fi
    split_dir="$(mktemp -d)"
    awk -v dir="$split_dir" '/BEGIN CERTIFICATE/ { n++ } n { print > (dir "/cert-" n ".pem") }' "$ca_bundle"
    for cert in "$split_dir"/cert-*.pem; do
        certutil -d "sql:$nss_db" -A -t "C,," -n "orbstack-$(basename "$cert" .pem)" -i "$cert"
    done
    rm -rf "$split_dir"
fi

exec "$@"
