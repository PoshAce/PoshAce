#!/bin/bash

# Poshace Magento Performance Optimization Script
# Run this script on your Magento server

echo "🚀 Starting Poshace Magento Performance Optimization..."

# Check if we're in the Magento root directory
if [ ! -f "bin/magento" ]; then
    echo "❌ Error: Please run this script from your Magento root directory"
    exit 1
fi

echo "📋 Step 1: Setting file permissions..."
find var generated pub/static pub/media app/etc -type f -exec chmod g+w {} +
find var generated pub/static pub/media app/etc -type d -exec chmod g+ws {} +

echo "📋 Step 2: Cleaning cache..."
php bin/magento cache:clean
php bin/magento cache:flush

echo "📋 Step 3: Reindexing..."
php bin/magento indexer:reindex

echo "📋 Step 4: Deploying static content..."
php bin/magento setup:static-content:deploy -f

echo "📋 Step 5: Compiling code..."
php bin/magento setup:di:compile

echo "📋 Step 6: Setting production mode..."
php bin/magento deploy:mode:set production

echo "📋 Step 7: Enabling all caches..."
php bin/magento cache:enable

echo "📋 Step 8: Optimizing database..."
php bin/magento setup:db-schema:upgrade
php bin/magento setup:db-data:upgrade

echo "📋 Step 9: Running cron jobs..."
php bin/magento cron:run

echo "📋 Step 10: Final cache clean..."
php bin/magento cache:clean
php bin/magento cache:flush

echo "✅ Magento optimization completed!"
echo ""
echo "🔧 Additional recommendations:"
echo "1. Enable Full Page Cache in Admin: Stores > Configuration > Advanced > System > Full Page Cache"
echo "2. Enable Flat Catalog in Admin: Stores > Configuration > Catalog > Catalog > Storefront"
echo "3. Configure Redis for session and cache storage"
echo "4. Set up a CDN for static assets"
echo "5. Optimize images and convert to WebP format"
echo ""
echo "📊 Performance monitoring tools:"
echo "- Google PageSpeed Insights: https://pagespeed.web.dev/"
echo "- GTmetrix: https://gtmetrix.com/"
echo "- WebPageTest: https://www.webpagetest.org/"