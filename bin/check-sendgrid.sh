#!/usr/bin/env bash
# Check SendGrid configuration and test API key

echo "==> Checking SendGrid Configuration..."

php artisan tinker --execute="
    echo '=== Mail Configuration ===\n';
    echo 'MAIL_MAILER: ' . env('MAIL_MAILER') . '\n';
    echo 'MAIL_HOST: ' . env('MAIL_HOST') . '\n';
    echo 'MAIL_PORT: ' . env('MAIL_PORT') . '\n';
    echo 'MAIL_USERNAME: ' . env('MAIL_USERNAME') . '\n';
    echo 'MAIL_PASSWORD: ' . (env('MAIL_PASSWORD') ? substr(env('MAIL_PASSWORD'), 0, 10) . '...' : 'NOT SET') . '\n';
    echo 'MAIL_ENCRYPTION: ' . env('MAIL_ENCRYPTION') . '\n';
    echo 'MAIL_FROM_ADDRESS: ' . env('MAIL_FROM_ADDRESS') . '\n';
    echo 'MAIL_FROM_NAME: ' . env('MAIL_FROM_NAME') . '\n';
    echo '\n';

    echo '=== Queue Configuration ===\n';
    echo 'QUEUE_CONNECTION: ' . env('QUEUE_CONNECTION') . '\n';
    echo '\n';
"

echo ""
echo "==> Checking Queue for pending emails..."

php artisan tinker --execute="
    \$pendingJobs = \DB::table('jobs')->count();
    echo 'Pending jobs in queue: ' . \$pendingJobs . '\n';

    if (\$pendingJobs > 0) {
        echo '\nFirst 5 jobs:\n';
        \$jobs = \DB::table('jobs')->limit(5)->get();
        foreach (\$jobs as \$job) {
            \$payload = json_decode(\$job->payload, true);
            echo '- ' . (\$payload['displayName'] ?? 'Unknown') . ' (attempts: ' . \$job->attempts . ')\n';
        }
    }
"

echo ""
echo "==> Checking Failed Jobs..."

php artisan tinker --execute="
    \$failedJobs = \DB::table('failed_jobs')->count();
    echo 'Failed jobs: ' . \$failedJobs . '\n';

    if (\$failedJobs > 0) {
        echo '\nRecent failed jobs:\n';
        \$jobs = \DB::table('failed_jobs')->orderBy('failed_at', 'desc')->limit(5)->get();
        foreach (\$jobs as \$job) {
            echo '- Failed at: ' . \$job->failed_at . '\n';
            echo '  Exception: ' . substr(\$job->exception, 0, 200) . '...\n\n';
        }
    }
"

echo ""
echo "==> Sending test email (NOT queued)..."

php artisan tinker --execute="
    try {
        \Illuminate\Support\Facades\Mail::raw('Test email from Fittable - ' . now(), function(\$message) {
            \$message->to('dixie.atay@gmail.com')
                    ->subject('Fittable Test Email - ' . now());
        });
        echo 'Email sent successfully to SMTP server!\n';
        echo 'Check SendGrid Activity at: https://app.sendgrid.com/email_activity\n';
    } catch (\Exception \$e) {
        echo 'ERROR: ' . \$e->getMessage() . '\n';
    }
"
