# NeonDB PostgreSQL Configuration

This application uses **NeonDB** (serverless PostgreSQL) for production database.

## Setup Instructions

### 1. Create NeonDB Account
1. Go to https://neon.tech and sign up
2. Create a new project
3. Select the region closest to your deployment
4. Copy the connection string from the project dashboard

### 2. Connection String Format

NeonDB provides a connection string in this format:
```
postgres://username:password@host.neon.tech/database?sslmode=require
```

### 3. Environment Configuration

Add to your `.env` or deployment environment:
```bash
DB_CONNECTION=pgsql
DB_URL=postgres://username:password@host.neon.tech/database?sslmode=require
```

**Important**: The `?sslmode=require` parameter is required for NeonDB connections.

### 4. Sevalla/Render Configuration

In your Sevalla dashboard:
1. Go to your web service
2. Navigate to Environment Variables
3. Add:
   - `DB_CONNECTION=pgsql`
   - `DB_URL=<your-neondb-connection-string>`

## Database Features

### SSL/TLS Required
NeonDB requires SSL connections. Ensure your connection string includes `?sslmode=require`.

### Connection Pooling
NeonDB automatically handles connection pooling. No additional configuration needed.

### Autoscaling
NeonDB scales compute and storage automatically based on usage.

### Backups
NeonDB provides:
- Continuous data protection
- Point-in-time recovery
- Automatic daily backups

## PostgreSQL vs MySQL Differences

This application is compatible with both PostgreSQL and MySQL, but note these differences:

### Data Types
- **MySQL**: Uses `TINYINT(1)` for booleans
- **PostgreSQL**: Uses native `BOOLEAN` type

### String Functions
- **MySQL**: `CONCAT()` and `CONCAT_WS()`
- **PostgreSQL**: `||` operator or `CONCAT()`

### JSON Operations
- **MySQL**: `JSON_EXTRACT()`, `->`
- **PostgreSQL**: `->>`, `#>>`, `jsonb` type

Laravel's query builder and Eloquent handle these differences automatically.

## Migration Considerations

All existing migrations are PostgreSQL-compatible. Key points:

1. **String Lengths**: Default is 255 (compatible)
2. **Timestamps**: Uses Laravel's standard timestamp format
3. **JSON Fields**: Uses Laravel's `json()` column type
4. **Foreign Keys**: Standard Laravel foreign key syntax

## Troubleshooting

### Connection Timeouts
- NeonDB may suspend inactive databases
- First connection after suspension takes longer (~5 seconds)
- Subsequent connections are instant

### SSL Errors
If you get SSL-related errors:
```bash
# Ensure sslmode is in your connection string
DB_URL=postgres://user:pass@host/db?sslmode=require
```

### Migration Errors
```bash
# Run migrations manually if needed
php artisan migrate --force
```

### Query Performance
- NeonDB optimizes for serverless
- Add indexes for frequently queried columns
- Monitor slow queries in NeonDB dashboard

## Local Development with PostgreSQL

To use PostgreSQL locally for testing:

### Using Docker
```bash
docker run --name postgres \
  -e POSTGRES_DB=fittable \
  -e POSTGRES_USER=postgres \
  -e POSTGRES_PASSWORD=password \
  -p 5432:5432 \
  -d postgres:16
```

### Using Homebrew (macOS)
```bash
brew install postgresql@16
brew services start postgresql@16
createdb fittable
```

### Local .env
```bash
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=fittable
DB_USERNAME=postgres
DB_PASSWORD=password
```

## Monitoring

### NeonDB Dashboard
Monitor these metrics in your NeonDB dashboard:
- Active connections
- Database size
- Query performance
- Compute usage

### Laravel Logs
Check database query logs:
```bash
php artisan log:tail
```

## Cost Optimization

### Free Tier Limits
- 0.5 GB storage
- 1 compute hour active time per month
- Automatic scale-to-zero when inactive

### Tips
1. Enable scale-to-zero for development databases
2. Use connection pooling efficiently
3. Optimize queries to reduce compute time
4. Monitor usage in NeonDB dashboard

## Resources

- [NeonDB Documentation](https://neon.tech/docs)
- [Laravel PostgreSQL](https://laravel.com/docs/database#postgresql)
- [Connection Pooling](https://neon.tech/docs/connect/connection-pooling)
