#!/bin/bash

cd /home/site/wwwroot

# Redirect all output to persistent log
exec >> /home/site/wwwroot/storage/logs/webjob-queue.log 2>&1

echo "[$(date)] Starting Laravel queue worker..."

# Sanity checks
if [ -z "$APP_KEY" ]; then
  echo "APP_KEY missing. Exiting."
  exit 1
fi

php artisan tinker --execute="Redis::ping();" || {
  echo "Redis not reachable. Exiting."
  exit 1
}

while true; do
  php artisan queue:work redis \
    --sleep=1 \
    --tries=3 \
    --timeout=90 \
    --max-jobs=100 \
    --max-time=3600

  echo "[$(date)] Queue worker exited. Restarting in 5 seconds..."
  sleep 5
done
