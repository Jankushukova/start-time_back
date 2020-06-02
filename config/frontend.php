<?php
return [
    'url' => env('FRONTEND_URL', 'https://localhost:4200'),
    // path to my frontend page with query param queryURL(temporarySignedRoute URL)
    'email_verify_url' => env('FRONTEND_EMAIL_VERIFY_URL', '/verify-email?queryURL='),
    'reset_password_url' => env('FRONTEND_RESET_PASSWORD_URL', '/reset/password?token='),
];
