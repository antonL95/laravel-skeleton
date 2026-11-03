#!/usr/bin/env bash
set -uo pipefail

if [ "$#" -eq 0 ]; then
    echo "usage: scripts/with-test-lock.sh <command...>" >&2
    exit 64
fi

if [ -n "${TEST_LOCK_HELD:-}" ]; then
    exec "$@"
fi

repo_root="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd -P)"
main_root="$(cd "$(git -C "$repo_root" rev-parse --path-format=absolute --git-common-dir 2>/dev/null || echo "$repo_root/.git")/.." && pwd -P)"
lock_dir="${TEST_LOCK_DIR:-/tmp/laravel-skeleton-test-run.lock}"

belongs_to_checkout() {
    case "$1" in
        "$repo_root" | "$main_root" | "$main_root"/.worktrees/*) return 0 ;;
        *) return 1 ;;
    esac
}

process_cwd() {
    if [ -r "/proc/$1/cwd" ]; then
        readlink "/proc/$1/cwd"
    else
        lsof -a -p "$1" -d cwd -Fn 2>/dev/null | sed -n 's/^n//p'
    fi
}

signal_orphaned_browsers() {
    local signal="$1" pid ppid command
    ps -axo pid=,ppid=,command= | while read -r pid ppid command; do
        [ "$ppid" = "1" ] || continue
        case "$command" in
            *"playwright run-server"* | *chrome-headless-shell*) ;;
            *) continue ;;
        esac
        belongs_to_checkout "$(process_cwd "$pid")" || continue
        kill "-$signal" "$pid" 2>/dev/null
        echo "$pid"
    done
}

reap_orphaned_browsers() {
    local stopped
    stopped="$(signal_orphaned_browsers TERM)"
    [ -n "$stopped" ] || return 0
    echo "Stopped orphaned test browser processes:" $stopped >&2
    sleep 1
    signal_orphaned_browsers KILL >/dev/null
}

holder_is_gone() {
    local output
    output="$(export LC_ALL=C; kill -0 "$1" 2>&1)" && return 1
    case "$output" in
        *"No such process"*) return 0 ;;
        *) return 1 ;;
    esac
}

announced=0
until mkdir "$lock_dir" 2>/dev/null; do
    holder="$(cat "$lock_dir/pid" 2>/dev/null || true)"
    if { [ -n "$holder" ] && holder_is_gone "$holder"; } || { [ -z "$holder" ] && [ -n "$(find "$lock_dir" -maxdepth 0 -mmin +1 2>/dev/null)" ]; }; then
        rm -rf "$lock_dir"
        continue
    fi
    if [ "$announced" -eq 0 ]; then
        echo "Waiting for another test run to finish: $(cat "$lock_dir/owner" 2>/dev/null || echo unknown)" >&2
        announced=1
    fi
    sleep 2
done

trap 'reap_orphaned_browsers; rm -rf "$lock_dir"' EXIT
echo "$$" > "$lock_dir/pid"
printf '%s (%s, since %s)\n' "$*" "$repo_root" "$(date '+%H:%M:%S')" > "$lock_dir/owner"
export TEST_LOCK_HELD=1

reap_orphaned_browsers
nice -n 10 "$@"
