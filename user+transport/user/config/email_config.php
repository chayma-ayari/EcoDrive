<?php
// SMTP Configuration (customize as needed)
return [
    'smtp' => [
        'host' => 'smtp.gmail.com',           // SMTP server (e.g., smtp.gmail.com for Gmail)
        'username' => 'tasnimouertani516@gmail.com', // Your email address
        'password' => 'xwmlkniterlmvlpg',    // Gmail app password or SMTP password
        'port' => 587,                        // SMTP port (587 for TLS)
        'secure' => 'tls',                    // Encryption (tls or ssl)
        'debug' => 2                          // Set to 2 for SMTP debug logs, 0 for production
    ],
    'from' => [
        'email' => 'tasnimouertani516@gmail.com',    // Sender email (same as username for Gmail)
        'name' => 'Password Reset System'     // Sender name
    ]
];
?>