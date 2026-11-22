<?php

// Copy this file to config.php and fill in the real values.
// config.php is not committed.

return [
    'db_host' => '127.0.0.1',
    'db_name' => 'library',
    'db_user' => 'root',
    'db_pass' => '',
    'jwt_secret' => 'change-this-secret-to-a-long-random-string',
    'jwt_hours' => 12,
    'frontend_url' => 'http://localhost:5500',
    'openlibrary_timeout' => 5,
    'openlibrary_cache_ttl' => 604800,
];
