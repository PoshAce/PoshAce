#!/bin/bash

# Magento 2.4.7 Performance Optimization Script
# Run this script to optimize your Magento installation for better performance

echo "==============================================="
echo "Magento 2.4.7 Performance Optimization Script"
echo "==============================================="

# Colors for output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Function to print colored output
print_status() {
    echo -e "${GREEN}[INFO]${NC} $1"
}

print_warning() {
    echo -e "${YELLOW}[WARNING]${NC} $1"
}

print_error() {
    echo -e "${RED}[ERROR]${NC} $1"
}

# Check if running as root or with sudo
if [[ $EUID -eq 0 ]]; then
    print_warning "Running as root. Some operations might need adjustment."
fi

# Set Magento root directory
MAGENTO_ROOT=$(pwd)
if [ ! -f "$MAGENTO_ROOT/bin/magento" ]; then
    print_error "Magento CLI not found. Please run this script from your Magento root directory."
    exit 1
fi

print_status "Magento root directory: $MAGENTO_ROOT"

# Function to run Magento CLI commands
run_magento_command() {
    local cmd="$1"
    local description="$2"
    
    print_status "$description"
    
    # Try to find PHP
    PHP_BINARY=""
    for php_cmd in php8.1 php8.0 php7.4 php; do
        if command -v $php_cmd &> /dev/null; then
            PHP_BINARY=$php_cmd
            break
        fi
    done
    
    if [ -z "$PHP_BINARY" ]; then
        print_warning "PHP not found in PATH. Trying with system PHP..."
        if [ -f "/usr/bin/php" ]; then
            PHP_BINARY="/usr/bin/php"
        else
            print_error "PHP binary not found. Please install PHP or add it to PATH."
            return 1
        fi
    fi
    
    print_status "Using PHP binary: $PHP_BINARY"
    
    if $PHP_BINARY bin/magento $cmd; then
        print_status "✓ $description completed successfully"
        return 0
    else
        print_error "✗ Failed to execute: $description"
        return 1
    fi
}

# 1. Set production mode
print_status "Setting Magento to production mode..."
run_magento_command "deploy:mode:set production --skip-compilation" "Setting production mode"

# 2. Enable all cache types
print_status "Enabling all cache types..."
run_magento_command "cache:enable" "Enabling cache types"

# 3. Clean and flush cache
print_status "Cleaning and flushing cache..."
run_magento_command "cache:clean" "Cleaning cache"
run_magento_command "cache:flush" "Flushing cache"

# 4. Compile DI
print_status "Compiling dependency injection..."
run_magento_command "setup:di:compile" "Compiling DI"

# 5. Deploy static content
print_status "Deploying static content..."
run_magento_command "setup:static-content:deploy -f" "Deploying static content"

# 6. Reindex all
print_status "Reindexing all indexes..."
run_magento_command "indexer:reindex" "Reindexing"

# 7. Set proper file permissions
print_status "Setting proper file permissions..."
find var generated vendor pub/static pub/media app/etc -type f -exec chmod g+w {} +
find var generated vendor pub/static pub/media app/etc -type d -exec chmod g+ws {} +
chown -R :www-data . 2>/dev/null || print_warning "Could not change group ownership to www-data"
chmod u+x bin/magento

# 8. Clear var/page_cache if it exists
if [ -d "var/page_cache" ]; then
    print_status "Clearing var/page_cache directory..."
    rm -rf var/page_cache/*
fi

# 9. Clear var/cache if it exists
if [ -d "var/cache" ]; then
    print_status "Clearing var/cache directory..."
    rm -rf var/cache/*
fi

# 10. Optimize composer autoloader
if [ -f "composer.phar" ]; then
    print_status "Optimizing Composer autoloader..."
    php composer.phar dump-autoload --optimize --apcu
elif command -v composer &> /dev/null; then
    print_status "Optimizing Composer autoloader..."
    composer dump-autoload --optimize --apcu
else
    print_warning "Composer not found. Skipping autoloader optimization."
fi

# 11. Database optimization suggestions
print_status "Database optimization suggestions:"
echo "  1. Ensure MySQL query cache is enabled"
echo "  2. Optimize MySQL configuration (my.cnf):"
echo "     - innodb_buffer_pool_size = 70% of RAM"
echo "     - query_cache_size = 256M"
echo "     - tmp_table_size = 256M"
echo "     - max_heap_table_size = 256M"

# 12. Create maintenance script
print_status "Creating daily maintenance script..."
cat > daily-maintenance.sh << 'EOF'
#!/bin/bash
# Daily Magento maintenance script

# Clean cache
php bin/magento cache:clean

# Reindex if needed
php bin/magento indexer:reindex

# Clean generated files older than 7 days
find generated/code/ -type f -mtime +7 -delete 2>/dev/null

# Clean log files older than 30 days
find var/log/ -type f -name "*.log" -mtime +30 -delete 2>/dev/null

echo "Daily maintenance completed at $(date)"
EOF

chmod +x daily-maintenance.sh
print_status "Daily maintenance script created: daily-maintenance.sh"

# 13. Performance monitoring script
print_status "Creating performance monitoring script..."
cat > performance-monitor.sh << 'EOF'
#!/bin/bash
# Magento performance monitoring script

echo "=== Magento Performance Report ==="
echo "Generated at: $(date)"
echo

# Check PHP OPcache status
echo "=== PHP OPcache Status ==="
php -r "
if (function_exists('opcache_get_status')) {
    \$status = opcache_get_status();
    if (\$status) {
        echo 'OPcache is enabled\n';
        echo 'Cache full: ' . (\$status['cache_full'] ? 'Yes' : 'No') . '\n';
        echo 'Hit rate: ' . round(\$status['opcache_statistics']['opcache_hit_rate'], 2) . '%\n';
    } else {
        echo 'OPcache is disabled\n';
    }
} else {
    echo 'OPcache is not available\n';
}
"

# Check cache status
echo -e "\n=== Cache Status ==="
php bin/magento cache:status

# Check indexer status
echo -e "\n=== Indexer Status ==="
php bin/magento indexer:status

# Check disk usage
echo -e "\n=== Disk Usage ==="
echo "pub/media: $(du -sh pub/media 2>/dev/null | cut -f1)"
echo "pub/static: $(du -sh pub/static 2>/dev/null | cut -f1)"
echo "var/: $(du -sh var/ 2>/dev/null | cut -f1)"
echo "generated/: $(du -sh generated/ 2>/dev/null | cut -f1)"

# Check Redis status (if available)
echo -e "\n=== Redis Status ==="
if command -v redis-cli &> /dev/null; then
    redis-cli ping 2>/dev/null || echo "Redis is not responding"
    redis-cli info memory 2>/dev/null | grep used_memory_human || echo "Cannot get Redis memory info"
else
    echo "Redis CLI not available"
fi

EOF

chmod +x performance-monitor.sh
print_status "Performance monitoring script created: performance-monitor.sh"

# Final recommendations
echo
echo "==============================================="
print_status "Optimization completed! Additional recommendations:"
echo
echo "1. Enable Redis for sessions and cache (see app/etc/env.php.optimized)"
echo "2. Use the optimized Nginx configuration (nginx.conf.optimized)"
echo "3. Apply the optimized PHP settings (php.ini)"
echo "4. Run ./daily-maintenance.sh daily via cron"
echo "5. Monitor performance with ./performance-monitor.sh"
echo "6. Consider using a CDN for static assets"
echo "7. Enable HTTP/2 on your web server"
echo "8. Use WebP images for better compression"
echo "9. Consider implementing Elasticsearch for catalog search"
echo "10. Monitor your site with tools like New Relic or DataDog"
echo
print_status "For cron job setup, add this to your crontab:"
echo "0 2 * * * /path/to/your/magento/daily-maintenance.sh >> /var/log/magento-maintenance.log 2>&1"
echo
echo "==============================================="