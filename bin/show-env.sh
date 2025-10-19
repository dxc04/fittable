#!/usr/bin/env bash
# Show actual environment variables

echo "==> Environment Variables (from system):"
echo "MAIL_MAILER: ${MAIL_MAILER:-NOT SET}"
echo "MAIL_HOST: ${MAIL_HOST:-NOT SET}"
echo "MAIL_PORT: ${MAIL_PORT:-NOT SET}"
echo "MAIL_USERNAME: ${MAIL_USERNAME:-NOT SET}"
echo "MAIL_PASSWORD: ${MAIL_PASSWORD:0:15}...${MAIL_PASSWORD: -10}"
echo "MAIL_ENCRYPTION: ${MAIL_ENCRYPTION:-NOT SET}"
echo "MAIL_FROM_ADDRESS: ${MAIL_FROM_ADDRESS:-NOT SET}"
echo "MAIL_FROM_NAME: ${MAIL_FROM_NAME:-NOT SET}"

echo ""
echo "==> Laravel env() values:"
php artisan tinker --execute="
    echo 'MAIL_MAILER: ' . env('MAIL_MAILER', 'NOT SET') . '\n';
    echo 'MAIL_HOST: ' . env('MAIL_HOST', 'NOT SET') . '\n';
    echo 'MAIL_PORT: ' . env('MAIL_PORT', 'NOT SET') . '\n';
    echo 'MAIL_USERNAME: ' . env('MAIL_USERNAME', 'NOT SET') . '\n';
    echo 'MAIL_PASSWORD length: ' . (env('MAIL_PASSWORD') ? strlen(env('MAIL_PASSWORD')) : 0) . '\n';
    echo 'MAIL_ENCRYPTION: ' . env('MAIL_ENCRYPTION', 'NOT SET') . '\n';
    echo 'MAIL_FROM_ADDRESS: ' . env('MAIL_FROM_ADDRESS', 'NOT SET') . '\n';
    echo 'MAIL_FROM_NAME: ' . env('MAIL_FROM_NAME', 'NOT SET') . '\n';
"

echo ""
echo "==> Laravel config() values:"
php artisan tinker --execute="
    echo 'mail.default: ' . config('mail.default') . '\n';
    echo 'mail.mailers.smtp.host: ' . config('mail.mailers.smtp.host') . '\n';
    echo 'mail.mailers.smtp.port: ' . config('mail.mailers.smtp.port') . '\n';
    echo 'mail.mailers.smtp.username: ' . config('mail.mailers.smtp.username') . '\n';
    echo 'mail.mailers.smtp.password length: ' . strlen(config('mail.mailers.smtp.password') ?? '') . '\n';
    echo 'mail.from.address: ' . config('mail.from.address') . '\n';
    echo 'mail.from.name: ' . config('mail.from.name') . '\n';
"
