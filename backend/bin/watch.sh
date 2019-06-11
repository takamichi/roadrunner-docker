#!/bin/sh

while true; do
  inotifywait \
    --quiet \
    --monitor \
    --event modify,close_write,move,create,delete \
    --format "%e %w%f" \
    --recursive "${APP_ROOT}" |
    while read -r LINE; do
      if (echo "${LINE}" | grep -E '^.*\.php$' >/dev/null); then
        exit 0
      fi
    done

    rr http:reset -w "${APP_ROOT}"
done
