<?php

# Hishebi API Configuration
# Copy this file from .env.example and update the values

# SQLite database path (absolute path recommended)
# Example: /path/to/your/database/hishebi.db
# On production, use a path outside the web root for security
if (!defined('DB_PATH')) {
    define('DB_PATH', __DIR__ . '/database/hishebi.db');
}

# API Authentication
if (!defined('API_KEY')) {
    define('API_KEY', 'change-this-secret-api-key-in-production');
}

# API Authentication type: 'bearer' or 'api_key'
if (!defined('API_AUTH_TYPE')) {
    define('API_AUTH_TYPE', 'bearer');
}

# Application settings
if (!defined('APP_NAME')) {
    define('APP_NAME', 'Hishebi API');
}
