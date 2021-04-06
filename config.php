<?php

return [
    'db' => [
        'driver' => getenv('DB_DRIVER'),
        'host' => getenv('DB_HOST'),
        'database' => getenv('DB_NAME'),
        'username' => getenv('DB_USERNAME'),
        'password' => getenv('DB_PASSWORD'),
        'charset' => getenv('DB_CHARSET'),
    ],
    'displayErrorDetails' => (bool) getenv('DISPLAY_ERRORS'),
    'timezone' => getenv('TIMEZONE'),
    'mail_address' => getenv('MAIL_ADDR'),
    'mail_password' => getenv('MAIL_PASS')
];
