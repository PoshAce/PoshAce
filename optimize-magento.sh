#!/bin/bash

# Magento 2 Performance Optimization Script
# Run this script to optimize your Magento installation for production

echo "============================================"
echo "Magento 2 Performance Optimization Script"
echo "============================================"

# Set variables
MAGE_ROOT="/workspace"
PHP_BIN="php"

# Function to run Magento commands
run_magento_cmd() {
    echo "Running: $1"
    cd $MAGE_ROOT && $PHP_BIN bin/magento $1
}

# 1. Enable Production Mode
echo ""
echo "1. Setting Production Mode..."
run_magento_cmd "deploy:mode:set production --skip-compilation"

# 2. Enable all caches
echo ""
echo "2. Enabling all cache types..."
run_magento_cmd "cache:enable"

# 3. Configure cache backend (Redis recommended)
echo ""
echo "3. Cache Backend Configuration..."
cat > $MAGE_ROOT/app/etc/cache-config.php << 'EOF'
<?php
return [
    'cache' => [
        'frontend' => [
            'default' => [
                'backend' => 'Magento\\Framework\\Cache\\Backend\\Redis',
                'backend_options' => [
                    'server' => '127.0.0.1',
                    'database' => '0',
                    'port' => '6379',
                    'password' => '',
                    'compress_data' => '1',
                    'compression_lib' => 'gzip',
                    'preload_keys' => '1'
                ]
            ]
        ]
    ]
];
EOF

# 4. Configure session storage (Redis)
echo ""
echo "4. Session Storage Configuration..."
run_magento_cmd "setup:config:set --session-save=redis --session-save-redis-host=127.0.0.1 --session-save-redis-port=6379 --session-save-redis-db=1"

# 5. Enable Full Page Cache
echo ""
echo "5. Configuring Full Page Cache..."
run_magento_cmd "config:set system/full_page_cache/caching_application 2" # 2 for Varnish, 1 for Built-in

# 6. JavaScript and CSS optimization
echo ""
echo "6. Optimizing JavaScript and CSS..."
run_magento_cmd "config:set dev/js/enable_js_bundling 1"
run_magento_cmd "config:set dev/js/minify_files 1"
run_magento_cmd "config:set dev/js/merge_files 1"
run_magento_cmd "config:set dev/css/minify_files 1"
run_magento_cmd "config:set dev/css/merge_css_files 1"

# 7. Enable static content signing
echo ""
echo "7. Enabling static content signing..."
run_magento_cmd "config:set dev/static/sign 1"

# 8. Image optimization settings
echo ""
echo "8. Configuring image optimization..."
run_magento_cmd "config:set dev/image/default_adapter GD2"
run_magento_cmd "config:set cms/wysiwyg/enabled disabled" # Disable for better performance

# 9. Database optimization
echo ""
echo "9. Optimizing database..."
run_magento_cmd "indexer:set-mode schedule"
run_magento_cmd "indexer:reindex"

# 10. Compile dependency injection
echo ""
echo "10. Compiling DI..."
run_magento_cmd "setup:di:compile"

# 11. Deploy static content
echo ""
echo "11. Deploying static content..."
run_magento_cmd "setup:static-content:deploy -f"

# 12. Clean and flush cache
echo ""
echo "12. Cleaning cache..."
run_magento_cmd "cache:clean"
run_magento_cmd "cache:flush"

# 13. Additional performance configurations
echo ""
echo "13. Additional performance settings..."
run_magento_cmd "config:set catalog/frontend/flat_catalog_category 1"
run_magento_cmd "config:set catalog/frontend/flat_catalog_product 1"

echo ""
echo "============================================"
echo "Optimization Complete!"
echo "============================================"
echo ""
echo "Next steps:"
echo "1. Install and configure Redis server"
echo "2. Install and configure Varnish (optional but recommended)"
echo "3. Configure your web server (nginx/apache) with the provided sample files"
echo "4. Set up a CDN for static content delivery"
echo "5. Monitor performance with New Relic or similar tools"
echo ""