#!/usr/bin/env bash
# Test email sending with current configuration

echo "==> Testing email configuration..."

php artisan tinker --execute="
    echo 'Mail Configuration:\n';
    echo 'MAIL_MAILER: ' . config('mail.default') . '\n';
    echo 'MAIL_HOST: ' . config('mail.mailers.smtp.host') . '\n';
    echo 'MAIL_PORT: ' . config('mail.mailers.smtp.port') . '\n';
    echo 'MAIL_USERNAME: ' . config('mail.mailers.smtp.username') . '\n';
    echo 'MAIL_PASSWORD: ' . (config('mail.mailers.smtp.password') ? 'SET (length: ' . strlen(config('mail.mailers.smtp.password')) . ')' : 'NOT SET') . '\n';
    echo 'MAIL_ENCRYPTION: ' . config('mail.mailers.smtp.encryption') . '\n';
    echo 'MAIL_FROM_ADDRESS: ' . config('mail.from.address') . '\n';
    echo 'MAIL_FROM_NAME: ' . config('mail.from.name') . '\n';
    echo '\n';
"

echo ""
echo "==> Sending test email..."

php artisan tinker --execute="
    try {
        \Mail::raw('This is a test email from Fittable.', function(\$message) {
            \$message->to('dixie.atay@gmail.com')
                    ->subject('Fittable Test Email');
        });
        echo 'Test email sent successfully!\n';
        echo 'Check your inbox at dixie.atay@gmail.com\n';
    } catch (\Exception \$e) {
        echo 'Email sending failed:\n';
        echo \$e->getMessage() . '\n';
        echo '\n';
        echo 'Full trace:\n';
        echo \$e->getTraceAsString() . '\n';
    }
"
