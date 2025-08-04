#!/bin/bash

# Magento 2 Performance Monitoring Script
# This script monitors various performance metrics

echo "============================================"
echo "Magento 2 Performance Monitoring"
echo "============================================"

# Variables
MAGE_ROOT="/workspace"
LOG_DIR="$MAGE_ROOT/var/log"
REPORT_FILE="$LOG_DIR/performance-report-$(date +%Y%m%d-%H%M%S).txt"

# Function to check PHP-FPM status
check_php_fpm() {
    echo "=== PHP-FPM Status ===" >> "$REPORT_FILE"
    if command -v php-fpm &> /dev/null; then
        ps aux | grep php-fpm | wc -l >> "$REPORT_FILE"
        echo "" >> "$REPORT_FILE"
    else
        echo "PHP-FPM not found" >> "$REPORT_FILE"
    fi
}

# Function to check Redis status
check_redis() {
    echo "=== Redis Status ===" >> "$REPORT_FILE"
    if command -v redis-cli &> /dev/null; then
        redis-cli ping >> "$REPORT_FILE" 2>&1
        redis-cli info memory | grep -E "used_memory_human|used_memory_peak_human" >> "$REPORT_FILE"
        redis-cli info stats | grep -E "instantaneous_ops_per_sec|total_commands_processed" >> "$REPORT_FILE"
        echo "" >> "$REPORT_FILE"
    else
        echo "Redis not installed" >> "$REPORT_FILE"
    fi
}

# Function to check Varnish status
check_varnish() {
    echo "=== Varnish Status ===" >> "$REPORT_FILE"
    if command -v varnishstat &> /dev/null; then
        varnishstat -1 | grep -E "cache_hit|cache_miss|n_object" >> "$REPORT_FILE"
        echo "" >> "$REPORT_FILE"
    else
        echo "Varnish not installed" >> "$REPORT_FILE"
    fi
}

# Function to check database performance
check_mysql() {
    echo "=== MySQL Performance ===" >> "$REPORT_FILE"
    if [ -f "$MAGE_ROOT/app/etc/env.php" ]; then
        # Extract database credentials
        DB_HOST=$(php -r "include '$MAGE_ROOT/app/etc/env.php'; echo \$_ENV['db']['connection']['default']['host'];")
        DB_NAME=$(php -r "include '$MAGE_ROOT/app/etc/env.php'; echo \$_ENV['db']['connection']['default']['dbname'];")
        DB_USER=$(php -r "include '$MAGE_ROOT/app/etc/env.php'; echo \$_ENV['db']['connection']['default']['username'];")
        DB_PASS=$(php -r "include '$MAGE_ROOT/app/etc/env.php'; echo \$_ENV['db']['connection']['default']['password'];")
        
        if command -v mysql &> /dev/null; then
            # Check slow queries
            mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" -e "SHOW GLOBAL STATUS LIKE 'Slow_queries';" >> "$REPORT_FILE" 2>/dev/null
            
            # Check table sizes
            mysql -h"$DB_HOST" -u"$DB_USER" -p"$DB_PASS" "$DB_NAME" -e "
                SELECT 
                    table_name AS 'Table',
                    ROUND(((data_length + index_length) / 1024 / 1024), 2) AS 'Size (MB)'
                FROM information_schema.tables
                WHERE table_schema = '$DB_NAME'
                ORDER BY (data_length + index_length) DESC
                LIMIT 10;" >> "$REPORT_FILE" 2>/dev/null
        fi
    fi
    echo "" >> "$REPORT_FILE"
}

# Function to check Magento cache status
check_magento_cache() {
    echo "=== Magento Cache Status ===" >> "$REPORT_FILE"
    cd "$MAGE_ROOT" && php bin/magento cache:status >> "$REPORT_FILE" 2>&1
    echo "" >> "$REPORT_FILE"
}

# Function to check disk usage
check_disk_usage() {
    echo "=== Disk Usage ===" >> "$REPORT_FILE"
    df -h "$MAGE_ROOT" >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
    echo "Large directories:" >> "$REPORT_FILE"
    du -sh "$MAGE_ROOT"/* | sort -hr | head -10 >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
}

# Function to check system resources
check_system_resources() {
    echo "=== System Resources ===" >> "$REPORT_FILE"
    echo "CPU Load:" >> "$REPORT_FILE"
    uptime >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
    
    echo "Memory Usage:" >> "$REPORT_FILE"
    free -h >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
    
    echo "Top Processes:" >> "$REPORT_FILE"
    ps aux --sort=-%cpu | head -10 >> "$REPORT_FILE"
    echo "" >> "$REPORT_FILE"
}

# Function to check page load times
check_page_load() {
    echo "=== Page Load Times ===" >> "$REPORT_FILE"
    URLS=(
        "/"
        "/customer/account/login"
        "/catalog/category/view/id/2"
    )
    
    for url in "${URLS[@]}"; do
        if command -v curl &> /dev/null; then
            echo "Testing: $url" >> "$REPORT_FILE"
            curl -o /dev/null -s -w "Total time: %{time_total}s\n" "http://localhost$url" >> "$REPORT_FILE" 2>&1
        fi
    done
    echo "" >> "$REPORT_FILE"
}

# Function to check error logs
check_error_logs() {
    echo "=== Recent Errors ===" >> "$REPORT_FILE"
    echo "PHP errors:" >> "$REPORT_FILE"
    if [ -f "/var/log/php_errors.log" ]; then
        tail -20 /var/log/php_errors.log >> "$REPORT_FILE" 2>/dev/null
    fi
    
    echo "" >> "$REPORT_FILE"
    echo "Magento exceptions:" >> "$REPORT_FILE"
    if [ -f "$LOG_DIR/exception.log" ]; then
        tail -20 "$LOG_DIR/exception.log" >> "$REPORT_FILE" 2>/dev/null
    fi
    echo "" >> "$REPORT_FILE"
}

# Generate recommendations
generate_recommendations() {
    echo "=== Recommendations ===" >> "$REPORT_FILE"
    
    # Check if production mode is enabled
    MODE=$(cd "$MAGE_ROOT" && php bin/magento deploy:mode:show 2>/dev/null | grep -o 'production\|developer\|default')
    if [ "$MODE" != "production" ]; then
        echo "⚠️  Switch to production mode for better performance" >> "$REPORT_FILE"
    fi
    
    # Check cache status
    DISABLED_CACHES=$(cd "$MAGE_ROOT" && php bin/magento cache:status 2>/dev/null | grep "0" | wc -l)
    if [ "$DISABLED_CACHES" -gt 0 ]; then
        echo "⚠️  Enable all cache types" >> "$REPORT_FILE"
    fi
    
    # Check Redis
    if ! command -v redis-cli &> /dev/null; then
        echo "⚠️  Install Redis for better caching performance" >> "$REPORT_FILE"
    fi
    
    # Check Varnish
    if ! command -v varnishstat &> /dev/null; then
        echo "⚠️  Consider installing Varnish for full-page caching" >> "$REPORT_FILE"
    fi
    
    echo "" >> "$REPORT_FILE"
}

# Main execution
echo "Generating performance report..."
echo "Report location: $REPORT_FILE"

echo "Magento 2 Performance Report" > "$REPORT_FILE"
echo "Generated: $(date)" >> "$REPORT_FILE"
echo "============================================" >> "$REPORT_FILE"
echo "" >> "$REPORT_FILE"

# Run all checks
check_system_resources
check_disk_usage
check_php_fpm
check_redis
check_varnish
check_mysql
check_magento_cache
check_page_load
check_error_logs
generate_recommendations

echo ""
echo "Performance report generated successfully!"
echo "View the report: cat $REPORT_FILE"
echo ""

# Display summary
echo "=== Quick Summary ==="
grep -E "⚠️|Error|Warning|Critical" "$REPORT_FILE" || echo "No critical issues found."
echo ""