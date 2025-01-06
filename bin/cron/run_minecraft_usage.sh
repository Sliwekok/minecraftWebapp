#!/bin/bash
while true; do
    php ../console minecraft:get-server-usage
    sleep 30

        # Check if the current time matches your desired weekly schedule
        CURRENT_DAY=$(date +%u) # Day of the week (1 = Monday, 7 = Sunday)
        CURRENT_HOUR=$(date +%H) # Current hour
        CURRENT_MIN=$(date +%M) # Current minute

        # Run the weekly command every Tuesday at 00:00
        if [ "$CURRENT_DAY" -eq 2 ] && [ "$CURRENT_HOUR" -eq 0 ] && [ "$CURRENT_MIN" -eq 0 ]; then
            php ../console minecraft:turn-off-minecraft-servers
        fi
done
