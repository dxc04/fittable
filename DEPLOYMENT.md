# Fittable Deployment Guide for Sevalla (Render)

## Prerequisites

- A Sevalla/Render account
- A Git repository with your code
- A Gemini API key
- Node.js 20.19+ or 22.12+ (automatically configured via .node-version file)

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
2. **Run migrations manually if needed** - If you see database errors:
   - Go to Render Shell (or connect via SSH)
   - Run: `php artisan migrate --force`
   - Run: `php artisan db:seed --class=RoleAndPermissionSeeder --force`
3. Test the registration and login functionality
4. **Configure email verification** - Users must verify their email before accessing protected features
5. Test the job analysis feature with your Gemini API key

### 6a. Manual Database Setup (if migrations fail during build)

If the build completes but you get database errors like "relation does not exist":

1. **Access the Render Shell**:
   - Go to your Sevalla/Render dashboard
   - Click on your web service
   - Click "Shell" in the top navigation

2. **Run migrations**:
   ```bash
   php artisan migrate --force
   ```

3. **Seed roles and permissions**:
   ```bash
   php artisan db:seed --class=RoleAndPermissionSeeder --force
   ```

4. **Clear caches**:
   ```bash
   php artisan config:clear
   php artisan cache:clear
   php artisan config:cache
   ```

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
2. Node.js version is 20.19+ or 22.12+ (check `.node-version` file)
3. All required extensions are available
4. Build script has execute permissions: `chmod +x bin/render-build.sh`

### Node.js Version Error

If you see "Vite requires Node.js version 20.19+ or 22.12+":
1. The `.node-version` file specifies Node 20.19.0
2. The `NODE_VERSION` environment variable is set in render.yaml
3. Render/Sevalla should automatically use this version
4. If not, manually set `NODE_VERSION=20.19.0` in your environment variables

### Database Connection Issues

1. Verify NeonDB database is accessible
2. Check `DB_URL` environment variable is set correctly with your NeonDB connection string
3. Ensure connection string includes `?sslmode=require` parameter
4. Try running migrations manually: `php artisan migrate --force`
5. Check NeonDB dashboard for connection limits and usage

### Cache Table Error ("relation cache does not exist")

This error occurs when the cache table hasn't been created:

1. **Access Render Shell** and run migrations:
   ```bash
   php artisan migrate --force
   ```

2. **Verify tables were created**:
   ```bash
   php artisan tinker
   >>> DB::table('cache')->count()
   ```

3. **If migrations won't run during build** (common issue):
   - The database might not be accessible during the build phase
   - Migrations must be run manually after the first deployment
   - Subsequent deployments will work normally

4. **Alternative: Use file cache for initial deployment**:
   - Temporarily set `CACHE_STORE=file` in environment variables
   - Deploy successfully
   - Run migrations manually
   - Change `CACHE_STORE` back to `database`

### Assets Not Loading

1. Ensure `npm run build` completed successfully
2. Verify `APP_URL` matches your Sevalla domain
3. Check browser console for CORS or mixed content errors

### Gemini API Errors

1. Verify `GEMINI_API_KEY` is set in environment variables
2. Check API key is valid and has sufficient quota
3. Monitor API usage in Google AI Studio

## Important Notes About Web Server

This application uses `php artisan serve` as defined in `render.yaml`. This is suitable for:
- Development environments
- Small production deployments on Render/Sevalla

For high-traffic production environments, consider:
- Using Render's native PHP runtime (no configuration needed)
- Or deploying to a platform with Nginx/Apache (Heroku, Laravel Forge, etc.)

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
