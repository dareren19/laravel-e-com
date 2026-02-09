#!/bin/bash
set -e

echo "🚀 Starting Laravel Deployment..."

# 1. Install essentials
apt-get update && apt-get install -y \
    curl wget ca-certificates \
    libpng-dev libonig-dev libxml2-dev \
    zip unzip git \
    default-mysql-client \
    && rm -rf /var/lib/apt/lists/*

echo "✓ System dependencies installed"

# 2. Download ALL SSL certificates TiDB might need
mkdir -p /etc/ssl/certs/tidbcloud
cd /etc/ssl/certs/tidbcloud

# Download AWS RDS bundle (TiDB Cloud uses AWS)
wget -q https://truststore.pki.rds.amazonaws.com/global/global-bundle.pem -O global-bundle.pem
wget -q https://letsencrypt.org/certs/isrgrootx1.pem -O isrgrootx1.pem

# Create a combined certificate
cat global-bundle.pem isrgrootx1.pem > combined-ca-bundle.pem

# Update system certificates
cp combined-ca-bundle.pem /usr/local/share/ca-certificates/tidbcloud.crt
update-ca-certificates

echo "✓ SSL certificates installed"

# 3. Go to app directory
cd /app

# 4. Composer install (skip platform reqs to avoid PHP version issues)
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs

echo "✓ Composer dependencies installed"

# 5. Create .env file
cat > .env << 'EOF'
APP_NAME="Laravel E-Commerce"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://${RAILWAY_STATIC_URL}

# Generate a random app key if not set
APP_KEY=base64:$(openssl rand -base64 32)

DB_CONNECTION=mysql
DB_HOST=gateway01.ap-southeast-1.prod.aws.tidbcloud.com
DB_PORT=4000
DB_DATABASE=laravel_e_com
DB_USERNAME=2kJJEgofwXCE1jo.root
DB_PASSWORD=${DB_PASSWORD}

# SSL for TiDB Cloud
MYSQL_ATTR_SSL_CA=/etc/ssl/certs/tidbcloud/combined-ca-bundle.pem
MYSQL_ATTR_SSL_VERIFY_SERVER_CERT=true
EOF

echo "✓ .env file created"

# 6. Create health check endpoint (BEFORE config cache)
cat > public/health.php << 'EOF'
<?php
// Simple health check that doesn't require database
http_response_code(200);
header('Content-Type: application/json');
echo json_encode([
    'status' => 'healthy',
    'service' => 'laravel',
    'timestamp' => time(),
    'database' => 'pending_setup'
]);
EOF

# Also create a root health endpoint
cat > public/index.php << 'EOF'
<?php
// Temporary root response
header('Location: /health');
EOF

echo "✓ Health endpoints created"

# 7. Create a simple route in routes/web.php (temporary)
cat > routes/web.php << 'EOF'
<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;

Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'app' => 'Laravel E-Commerce',
        'time' => now()->toDateTimeString()
    ]);
});

Route::get('/', function () {
    return view('welcome');
});

// Test database connection
Route::get('/test-db', function () {
    try {
        DB::connection()->getPdo();
        return response()->json([
            'status' => 'connected',
            'database' => DB::connection()->getDatabaseName()
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});
EOF

echo "✓ Routes configured"

# 8. Cache config (skip if fails)
php artisan config:cache || true
php artisan route:cache || true

echo "✓ Configuration cached"

# 9. Set permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✓ Permissions set"

# 10. Create a simple database test script
cat > database-test.php << 'EOF'
<?php
require __DIR__.'/vendor/autoload.php';

use Illuminate\Support\Facades\DB;

try {
    // Test with raw PDO first
    $pdo = new PDO(
        "mysql:host=" . env('DB_HOST') . ";port=" . env('DB_PORT') . ";dbname=" . env('DB_DATABASE'),
        env('DB_USERNAME'),
        env('DB_PASSWORD'),
        [
            PDO::MYSQL_ATTR_SSL_CA => env('MYSQL_ATTR_SSL_CA'),
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ]
    );
    echo "✅ Direct PDO connection successful!\n";
    
    // Test Laravel DB connection
    DB::connection()->getPdo();
    echo "✅ Laravel DB connection successful!\n";
    
} catch (Exception $e) {
    echo "❌ Connection failed: " . $e->getMessage() . "\n";
    echo "SSL CA Path: " . env('MYSQL_ATTR_SSL_CA') . "\n";
    echo "File exists: " . (file_exists(env('MYSQL_ATTR_SSL_CA')) ? 'YES' : 'NO') . "\n";
}
EOF

echo "✓ Database test script created"

# 11. Test the SSL certificate exists
echo "Checking SSL certificate..."
ls -la /etc/ssl/certs/tidbcloud/combined-ca-bundle.pem

# 12. Start the server
echo "🚀 Starting Laravel server on port ${PORT}..."
exec php artisan serve --host=0.0.0.0 --port=${PORT}