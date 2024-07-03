#!/bin/bash

# Open Laravel server in a new tab
start cmd //c "php artisan serve"

# Open Queue worker in a new tab
start cmd //c "php artisan queue:work"

# Open WebSocket server in a new tab
start cmd //c "php artisan websocket:serve"
