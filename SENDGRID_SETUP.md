# SendGrid Email Configuration

This application is configured to use SendGrid for transactional emails (registration, email verification, password resets, etc.).

## Prerequisites

1. **SendGrid Account**: Sign up at https://sendgrid.com
2. **API Key**: Create an API key with "Mail Send" permissions
3. **Verified Sender**: Verify your sender email address or domain

## Setup Steps

### 1. Create SendGrid API Key

1. Log into SendGrid dashboard
2. Go to **Settings** → **API Keys**
3. Click **Create API Key**
4. Name: `Fittable Production`
5. Permissions: **Restricted Access** → Enable **Mail Send** → **Full Access**
6. Click **Create & View**
7. **Copy the API key** (you won't be able to see it again!)

### 2. Verify Sender Identity

**Option A: Single Sender Verification** (Easier, free tier)
1. Go to **Settings** → **Sender Authentication** → **Single Sender Verification**
2. Click **Create New Sender**
3. Fill in your details:
   - From Name: `Fittable`
   - From Email: `noreply@yourdomain.com` (must be an email you control)
   - Reply To: Your support email
4. Check your email and click the verification link

**Option B: Domain Authentication** (Recommended for production)
1. Go to **Settings** → **Sender Authentication** → **Authenticate Your Domain**
2. Follow the wizard to add DNS records to your domain
3. Wait for DNS propagation (up to 48 hours)

### 3. Configure Environment Variables in Render

Go to your Render dashboard → Your web service → Environment:

#### Required Variables (set these manually):

```bash
MAIL_PASSWORD=your_sendgrid_api_key_here
MAIL_FROM_ADDRESS=noreply@yourdomain.com
```

**Note**:
- `MAIL_PASSWORD` should be your SendGrid API key (starts with `SG.`)
- `MAIL_FROM_ADDRESS` must match your verified sender email

#### Pre-configured Variables (already in render.yaml):

These are automatically configured:
```bash
MAIL_MAILER=smtp
MAIL_HOST=smtp.sendgrid.net
MAIL_PORT=587
MAIL_USERNAME=apikey
MAIL_ENCRYPTION=tls
MAIL_FROM_NAME=Fittable
```

### 4. Configure Worker Service

The worker service needs the same email credentials to send queued emails.

In Render dashboard → Worker service → Environment:

Set these to **sync from web service**:
- `MAIL_PASSWORD`
- `MAIL_FROM_ADDRESS`

### 5. Test Email Sending

After deployment, test the email functionality:

1. **Register a new account** - Should send verification email
2. **Request password reset** - Should send reset link
3. **Check SendGrid Activity** - Go to SendGrid dashboard → Activity to see sent emails

## Troubleshooting

### Emails Not Sending

1. **Check SendGrid Activity Feed**:
   - Go to SendGrid dashboard → Email Activity
   - Look for failed sends or bounces

2. **Check Laravel Logs**:
   ```bash
   # In Render Shell
   php artisan log:tail
   ```

3. **Common Issues**:
   - **401 Unauthorized**: Wrong API key
   - **403 Forbidden**: Sender not verified
   - **550 Unauthenticated**: From address doesn't match verified sender

### Testing Emails in Render Shell

```bash
php artisan tinker --execute="
    \Mail::raw('Test email from Fittable', function(\$message) {
        \$message->to('your-email@example.com')
                ->subject('Test Email');
    });
    echo 'Email sent!\n';
"
```

## SendGrid Best Practices

1. **Monitor Your Reputation**: Check SendGrid's reputation dashboard regularly
2. **Set Up Webhooks**: Configure event webhooks for bounces and spam reports
3. **Implement Unsubscribe**: Add unsubscribe links for marketing emails
4. **Warm Up Your Domain**: Gradually increase sending volume when starting
5. **Monitor Quotas**: Free tier has 100 emails/day limit

## Cost Considerations

- **Free Tier**: 100 emails/day forever
- **Essentials**: $19.95/mo for 50,000 emails/mo
- **Pro**: Starting at $89.95/mo for 100,000 emails/mo

For most applications starting out, the free tier is sufficient.

## Security Notes

- ✅ **Never commit API keys** to version control
- ✅ API keys are stored as environment variables in Render
- ✅ SMTP connection uses TLS encryption
- ✅ Render environment variables are encrypted at rest

## Additional Resources

- [SendGrid Documentation](https://docs.sendgrid.com)
- [Laravel Mail Documentation](https://laravel.com/docs/12.x/mail)
- [SendGrid API Key Security](https://docs.sendgrid.com/ui/account-and-settings/api-keys#api-key-permissions)
