#!/usr/bin/env bash
# Direct SendGrid API test

echo "==> Testing SendGrid API directly with curl..."

php artisan tinker --execute="
    \$apiKey = env('MAIL_PASSWORD');
    \$fromEmail = env('MAIL_FROM_ADDRESS');

    echo 'API Key: ' . substr(\$apiKey, 0, 15) . '...' . substr(\$apiKey, -10) . '\n';
    echo 'From Email: ' . \$fromEmail . '\n';
    echo '\n';

    // Test via curl
    \$data = [
        'personalizations' => [
            [
                'to' => [['email' => 'dixie.atay@gmail.com']],
                'subject' => 'Direct SendGrid API Test - ' . now()
            ]
        ],
        'from' => ['email' => \$fromEmail],
        'content' => [
            [
                'type' => 'text/plain',
                'value' => 'This is a direct API test from Fittable at ' . now()
            ]
        ]
    ];

    \$ch = curl_init('https://api.sendgrid.com/v3/mail/send');
    curl_setopt(\$ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt(\$ch, CURLOPT_POST, true);
    curl_setopt(\$ch, CURLOPT_HTTPHEADER, [
        'Authorization: Bearer ' . \$apiKey,
        'Content-Type: application/json'
    ]);
    curl_setopt(\$ch, CURLOPT_POSTFIELDS, json_encode(\$data));

    \$response = curl_exec(\$ch);
    \$httpCode = curl_getinfo(\$ch, CURLINFO_HTTP_CODE);
    \$error = curl_error(\$ch);
    curl_close(\$ch);

    echo 'HTTP Status Code: ' . \$httpCode . '\n';

    if (\$httpCode === 202) {
        echo 'SUCCESS! Email accepted by SendGrid.\n';
        echo 'Check your inbox at dixie.atay@gmail.com\n';
        echo 'Also check SendGrid Activity: https://app.sendgrid.com/email_activity\n';
    } else {
        echo 'FAILED!\n';
        echo 'Response: ' . \$response . '\n';
        if (\$error) {
            echo 'Curl Error: ' . \$error . '\n';
        }
    }
"

echo ""
echo "==> Testing via Laravel Mail facade..."

php artisan tinker --execute="
    \Illuminate\Support\Facades\Mail::raw('Laravel Mail test at ' . now(), function(\$message) {
        \$message->to('dixie.atay@gmail.com')
                ->subject('Laravel Mail Test - ' . now());
    });
    echo 'Laravel Mail::raw executed (check if it threw any errors above)\n';
"

echo ""
echo "==> Checking mail driver configuration..."

php artisan tinker --execute="
    \$config = config('mail.mailers.smtp');
    echo 'Host: ' . \$config['host'] . '\n';
    echo 'Port: ' . \$config['port'] . '\n';
    echo 'Username: ' . \$config['username'] . '\n';
    echo 'Password set: ' . (!empty(\$config['password']) ? 'YES (' . strlen(\$config['password']) . ' chars)' : 'NO') . '\n';
    echo 'Encryption: ' . \$config['encryption'] . '\n';
    echo '\n';
    echo 'From address: ' . config('mail.from.address') . '\n';
    echo 'From name: ' . config('mail.from.name') . '\n';
"
