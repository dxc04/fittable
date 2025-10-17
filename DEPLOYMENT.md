# Fittable Deployment Guide for Sevalla (Render)

## Prerequisites

- A Sevalla/Render account
- A Git repository with your code
- A Gemini API key

## Deployment Steps

### 1. Connect Your Repository

1. Log in to your Sevalla/Render dashboard
2. Click "New +" and select "Web Service"
3. Connect your Git repository (GitHub, GitLab, or Bitbucket)
4. Select the `fittable` repository

### 2. Configure the Service

Sevalla will automatically detect the `render.yaml` configuration file. Verify these settings:

- **Name**: fittable
- **Runtime**: PHP
- **Build Command**: `./bin/render-build.sh`
- **Start Command**: `php artisan serve --host=0.0.0.0 --port=$PORT`

### 3. Set Up Database with NeonDB

This application uses **NeonDB** (serverless PostgreSQL) for production database:

1. **Create a NeonDB Database**:
   - Go to https://neon.tech and sign up
   - Create a new project
   - Copy your connection string (it looks like: `postgres://user:password@host/database?sslmode=require`)

2. **Configure Database in Sevalla**:
   - In your Sevalla dashboard, go to Environment Variables
   - Add `DB_URL` with your NeonDB connection string
   - The application will automatically use PostgreSQL via the connection string

### 4. Configure Environment Variables

Add these **required** environment variables in your Sevalla dashboard:

```bash
# Application
APP_NAME=Fittable
APP_ENV=production
APP_DEBUG=false
APP_URL=https://your-app-name.onrender.com

# Database (NeonDB connection string)
DB_CONNECTION=pgsql
DB_URL=postgres://user:password@your-neondb-host.neon.tech/database?sslmode=require

# Gemini AI (REQUIRED)
GEMINI_API_KEY=your_gemini_api_key_here

# Mail Configuration (REQUIRED for email verification)
MAIL_MAILER=smtp
MAIL_HOST=smtp.mailtrap.io
MAIL_PORT=2525
MAIL_USERNAME=your_mail_username
MAIL_PASSWORD=your_mail_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@fittable.app
MAIL_FROM_NAME="${APP_NAME}"
```

**Note**:
- Sevalla will automatically generate the `APP_KEY`
- Use your NeonDB connection string for `DB_URL`

### 5. Deploy

1. Click "Create Web Service"
2. Sevalla will automatically:
   - Install Composer dependencies
   - Install Node dependencies and build assets
   - Run database migrations
   - Seed roles and permissions
   - Cache configuration

### 6. Post-Deployment

After the first successful deployment:

1. Verify the application is running at your Sevalla URL
2. Test the registration and login functionality
3. **Configure email verification** - Users must verify their email before accessing protected features
4. Test the job analysis feature with your Gemini API key

### 7. Email Verification Setup

The application requires email verification for all new users. To configure:

1. **For Development/Testing**: Use Mailtrap
   - Sign up at https://mailtrap.io
   - Get your SMTP credentials
   - Add to environment variables:
     ```
     MAIL_MAILER=smtp
     MAIL_HOST=sandbox.smtp.mailtrap.io
     MAIL_PORT=2525
     MAIL_USERNAME=your_mailtrap_username
     MAIL_PASSWORD=your_mailtrap_password
     MAIL_ENCRYPTION=tls
     ```

2. **For Production**: Use a transactional email service
   - **Recommended**: SendGrid, Mailgun, or Amazon SES
   - Configure SMTP credentials in environment variables
   - Example for SendGrid:
     ```
     MAIL_MAILER=smtp
     MAIL_HOST=smtp.sendgrid.net
     MAIL_PORT=587
     MAIL_USERNAME=apikey
     MAIL_PASSWORD=your_sendgrid_api_key
     MAIL_ENCRYPTION=tls
     MAIL_FROM_ADDRESS=noreply@your domain.com
     MAIL_FROM_NAME="Fittable"
     ```

## Important Notes

### Queue Worker

For background job processing (resume analysis, etc.), you'll need to add a background worker:

1. In your Sevalla dashboard, create a new **Background Worker**
2. Use this start command:
   ```bash
   php artisan queue:work --tries=3 --timeout=300
   ```

### File Storage

The application uses local storage by default. For production, consider:
- Uploading files to AWS S3 or another cloud storage
- Update `FILESYSTEM_DISK=s3` in environment variables
- Add AWS credentials

### Database Backups

Enable automatic database backups in your Sevalla dashboard under Database settings.

### Monitoring

- Enable health checks in Sevalla dashboard
- Monitor error logs: `php artisan log:tail` or check Sevalla logs
- Set up alerts for downtime

## Troubleshooting

### Build Fails

If the build fails, check:
1. PHP version is 8.2+ in your `composer.json`
2. All required extensions are available
3. Build script has execute permissions: `chmod +x bin/render-build.sh`

### Database Connection Issues

1. Verify NeonDB database is accessible
2. Check `DB_URL` environment variable is set correctly with your NeonDB connection string
3. Ensure connection string includes `?sslmode=require` parameter
4. Try running migrations manually: `php artisan migrate --force`
5. Check NeonDB dashboard for connection limits and usage

### Assets Not Loading

1. Ensure `npm run build` completed successfully
2. Verify `APP_URL` matches your Sevalla domain
3. Check browser console for CORS or mixed content errors

### Gemini API Errors

1. Verify `GEMINI_API_KEY` is set in environment variables
2. Check API key is valid and has sufficient quota
3. Monitor API usage in Google AI Studio

## Alternative Deployment (Heroku-style)

If using a Heroku-style platform, the `Procfile` is already configured:

```
web: vendor/bin/heroku-php-apache2 public/
worker: php artisan queue:work --tries=3 --timeout=300
```

## Updating Your Deployment

To deploy updates:
1. Push changes to your Git repository
2. Sevalla will automatically detect changes and redeploy
3. Manual deployments: Click "Manual Deploy" in dashboard

## Environment Variables Reference

| Variable | Required | Description |
|----------|----------|-------------|
| APP_NAME | No | Application name |
| APP_ENV | Yes | Set to `production` |
| APP_DEBUG | Yes | Set to `false` |
| APP_URL | Yes | Your Sevalla URL |
| APP_KEY | Auto | Auto-generated by Sevalla |
| GEMINI_API_KEY | **Yes** | Your Gemini API key |
| DB_CONNECTION | Yes | Set to `pgsql` |
| DB_URL | **Yes** | Your NeonDB connection string |
| MAIL_* | **Yes** | Required for email verification |

## Security Checklist

- [ ] `APP_DEBUG=false` in production
- [ ] `APP_ENV=production`
- [ ] Database credentials are secure
- [ ] SSL/HTTPS is enabled (automatic on Sevalla)
- [ ] GEMINI_API_KEY is kept secret
- [ ] Regular database backups are enabled
- [ ] Error logging is configured

## Support

For issues specific to:
- **Sevalla/Render**: Check [Render documentation](https://render.com/docs)
- **Laravel**: Check [Laravel documentation](https://laravel.com/docs)
- **Gemini API**: Check [Google AI Studio](https://ai.google.dev/)
