#!/bin/sh

# POSIX signal names (dash's trap rejects the SIG prefix)
trap "trap - TERM && kill -- -$$" INT TERM EXIT

OPTIONS=''

# case instead of [ $1 = ... ]: no unbound-variable error when run without argument
case "${1:-}" in
    -v|-vv|--verbose|--dev) OPTIONS="-vv" ;;
esac

php bin/console messenger:consume async_priority_high $OPTIONS &
php bin/console messenger:consume async_priority_medium $OPTIONS &
php bin/console messenger:consume async_priority_low $OPTIONS
