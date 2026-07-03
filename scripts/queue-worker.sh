#!/bin/sh
cd "$(dirname "$0")/.." || exit 1

LOG_FILE="storage/logs/queue-worker.log"

echo "[$(date '+%Y-%m-%d %H:%M:%S')] watchdog started, pid $$" >> "$LOG_FILE"

while true; do
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] starting queue:work" >> "$LOG_FILE"
  php -d memory_limit=512M artisan queue:work \
    --stop-when-empty \
    --max-jobs=25 \
    --max-time=600 \
    --memory=384 \
    --tries=3 \
    --sleep=3 >> "$LOG_FILE" 2>&1
  EXIT_CODE=$?
  echo "[$(date '+%Y-%m-%d %H:%M:%S')] queue:work exited with code $EXIT_CODE" >> "$LOG_FILE"
  sleep 5
done
