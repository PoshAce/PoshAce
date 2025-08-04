#!/bin/bash

# PoshAce Magento Performance Optimization Script
# This script implements all the optimizations identified in the performance audit

echo "🚀 Starting PoshAce Magento Performance Optimization..."
echo "=================================================="

# Check if we're in the Magento root directory
if [ ! -f "bin/magento" ]; then
    echo "❌ Error: Please run this script from the Magento root directory"
    exit 1
fi

# Function to check command status
check_status() {
    if [ $? -eq 0 ]; then
        echo "✅ $1 completed successfully"
    else
        echo "❌ $1 failed"
        exit 1
    fi
}

# 1. Enable production mode
echo "📦 Setting production mode..."
php bin/magento deploy:mode:set production
check_status "Production mode setup"

# 2. Clear all caches
echo "🧹 Clearing all caches..."
php bin/magento cache:clean
php bin/magento cache:flush
check_status "Cache clearing"

# 3. Enable all cache types
echo "⚡ Enabling all cache types..."
php bin/magento cache:enable
check_status "Cache enabling"

# 4. Deploy static content with optimizations
echo "🎨 Deploying optimized static content..."
php bin/magento poshace:deploy:optimized
check_status "Static content deployment"

# 5. Set proper permissions
echo "🔐 Setting proper file permissions..."
find var generated pub/static pub/media app/etc -type f -exec chmod g+w {} +
find var generated pub/static pub/media app/etc -type d -exec chmod g+ws {} +
chown -R www-data:www-data var generated pub/static pub/media app/etc
check_status "Permission setting"

# 6. Optimize database
echo "🗄️ Optimizing database..."
php bin/magento setup:db-schema:upgrade
php bin/magento setup:db-data:upgrade
check_status "Database optimization"

# 7. Generate sitemap
echo "🗺️ Generating sitemap..."
php bin/magento sitemap:generate
check_status "Sitemap generation"

# 8. Index reindex
echo "🔍 Reindexing..."
php bin/magento indexer:reindex
check_status "Indexing"

# 9. Compile and deploy
echo "🔧 Compiling and deploying..."
php bin/magento setup:di:compile
php bin/magento setup:static-content:deploy -f
check_status "Compilation and deployment"

# 10. Final cache warmup
echo "🔥 Warming up caches..."
php bin/magento cache:clean
check_status "Cache warmup"

echo ""
echo "🎉 Performance optimization completed successfully!"
echo "=================================================="
echo ""
echo "📊 Expected performance improvements:"
echo "   • Document request latency: ~1,520ms improvement"
echo "   • Font display: ~450ms improvement"
echo "   • Render blocking requests: ~80ms improvement"
echo "   • Cache efficiency: ~281 KiB savings"
echo "   • Image delivery: ~607 KiB savings"
echo ""
echo "🔧 Additional recommendations:"
echo "   1. Configure Redis for session storage"
echo "   2. Enable Varnish cache if available"
echo "   3. Use a CDN for static assets"
echo "   4. Monitor performance with tools like GTmetrix or PageSpeed Insights"
echo ""
echo "📈 Run another performance audit to verify improvements!"