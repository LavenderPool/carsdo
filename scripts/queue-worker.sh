#!/bin/sh
cd "$(dirname "$0")/.." || exit 1

while true; do
  php artisan queue:work \
    --stop-when-empty \
    --max-jobs=25 \
    --max-time=600 \
    --memory=256 \
    --tries=3 \
    --sleep=3 >> storage/logs/queue-worker.log 2>&1
  sleep 5
done
