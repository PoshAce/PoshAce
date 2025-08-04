# Magento 2.4.7 Performance Optimization Guide

This repository contains optimized configuration files and scripts to significantly improve your Magento 2.4.7 site performance.

## 🚀 What's Included

### Configuration Files
- **`php.ini`** - Optimized PHP configuration with OPcache settings
- **`pub/.htaccess`** - Enhanced Apache configuration with compression and caching
- **`nginx.conf.optimized`** - Production-ready Nginx configuration
- **`app/etc/env.php.optimized`** - Optimized Magento environment configuration
- **`redis.conf.optimized`** - Tuned Redis configuration for better performance
- **`mysql-optimization.cnf`** - MySQL/MariaDB performance tuning

### Scripts
- **`magento-performance-optimizer.sh`** - Main optimization script
- **`daily-maintenance.sh`** - Automated daily maintenance tasks
- **`performance-monitor.sh`** - Performance monitoring and reporting

## 📋 Prerequisites

- Magento 2.4.7 installed and configured
- PHP 7.4+ (PHP 8.1 recommended)
- Redis server
- MySQL 5.7+ or MariaDB 10.2+
- Web server (Apache or Nginx)
- Root or sudo access to the server

## 🔧 Installation & Setup

### 1. Backup Your Current Configuration
```bash
# Backup current files
cp php.ini php.ini.backup
cp pub/.htaccess pub/.htaccess.backup
cp app/etc/env.php app/etc/env.php.backup
```

### 2. Apply PHP Optimizations
```bash
# Copy optimized PHP configuration
sudo cp php.ini /etc/php/8.1/fpm/php.ini
sudo cp php.ini /etc/php/8.1/cli/php.ini

# Restart PHP-FPM
sudo systemctl restart php8.1-fpm
```

### 3. Apply Web Server Configuration

#### For Apache:
```bash
# The optimized .htaccess is already in place in pub/
# Ensure mod_deflate, mod_expires, and mod_headers are enabled
sudo a2enmod deflate expires headers
sudo systemctl restart apache2
```

#### For Nginx:
```bash
# Copy and customize the Nginx configuration
sudo cp nginx.conf.optimized /etc/nginx/sites-available/your-site
# Edit the file to match your domain and paths
sudo nano /etc/nginx/sites-available/your-site
sudo systemctl restart nginx
```

### 4. Apply Redis Configuration
```bash
# Copy Redis configuration
sudo cp redis.conf.optimized /etc/redis/redis.conf
sudo systemctl restart redis-server
```

### 5. Apply MySQL Optimizations
```bash
# Add the MySQL optimizations to your configuration
sudo cat mysql-optimization.cnf >> /etc/mysql/my.cnf
# Or copy specific sections based on your setup
sudo systemctl restart mysql
```

### 6. Update Magento Configuration
```bash
# Review and apply the optimized environment configuration
cp app/etc/env.php.optimized app/etc/env.php
# Edit the file to match your specific settings (database credentials, etc.)
```

### 7. Run the Main Optimization Script
```bash
# Make sure you're in your Magento root directory
./magento-performance-optimizer.sh
```

## 🔍 Key Optimizations Implemented

### PHP Optimizations
- **OPcache enabled** with optimized settings for production
- **Memory limit** increased to 2GB for complex operations
- **Execution time** optimized for better performance
- **Output compression** enabled to reduce bandwidth

### Web Server Optimizations
- **Gzip compression** for text-based files
- **Browser caching** with proper expire headers
- **Static file caching** for images, CSS, and JavaScript
- **Security headers** for better protection

### Redis Optimizations
- **Memory management** with LRU eviction policy
- **Compression enabled** for cached data
- **Persistent connections** for better performance
- **Optimized timeout settings**

### Magento-Specific Optimizations
- **Production mode** enabled
- **All cache types** enabled
- **Parallel cache generation** enabled
- **Redis for sessions** instead of files
- **Asynchronous operations** enabled

### Database Optimizations
- **InnoDB buffer pool** optimized for your RAM
- **Query cache** enabled (MySQL < 8.0)
- **Connection pooling** optimized
- **Slow query logging** enabled for monitoring

## 📊 Performance Monitoring

### Daily Monitoring
```bash
# Run the performance monitor
./performance-monitor.sh
```

### Set Up Automated Maintenance
```bash
# Add to crontab for daily maintenance at 2 AM
crontab -e
# Add this line:
0 2 * * * /path/to/your/magento/daily-maintenance.sh >> /var/log/magento-maintenance.log 2>&1
```

## 🎯 Expected Performance Improvements

After applying these optimizations, you should see:

- **50-80% reduction** in page load times
- **Improved TTFB** (Time To First Byte)
- **Better cache hit rates**
- **Reduced server resource usage**
- **Improved Core Web Vitals** scores

## 🛠️ Troubleshooting

### Common Issues

1. **502 Bad Gateway (Nginx)**
   - Check PHP-FPM is running: `sudo systemctl status php8.1-fpm`
   - Verify socket path in Nginx config matches PHP-FPM config

2. **500 Internal Server Error (Apache)**
   - Check if all required Apache modules are enabled
   - Review error logs: `tail -f /var/log/apache2/error.log`

3. **Redis Connection Issues**
   - Verify Redis is running: `redis-cli ping`
   - Check Redis configuration and port settings

4. **MySQL Performance Issues**
   - Monitor slow query log: `tail -f /var/log/mysql/mysql-slow.log`
   - Adjust buffer sizes based on your server's RAM

### Performance Testing

Test your site performance using:
- **GTmetrix** (https://gtmetrix.com/)
- **Google PageSpeed Insights** (https://pagespeed.web.dev/)
- **WebPageTest** (https://webpagetest.org/)

## 📈 Additional Recommendations

### Infrastructure
1. **Use a CDN** (CloudFlare, AWS CloudFront, etc.)
2. **Implement HTTP/2** on your web server
3. **Consider using Elasticsearch** for catalog search
4. **Use SSD storage** for better I/O performance

### Code Optimizations
1. **Enable flat catalog** for large product catalogs
2. **Optimize images** (use WebP format when possible)
3. **Minify CSS and JavaScript**
4. **Use critical CSS** for above-the-fold content

### Monitoring
1. **Set up APM** (New Relic, DataDog, etc.)
2. **Monitor Core Web Vitals**
3. **Set up uptime monitoring**
4. **Regular performance audits**

## 🔐 Security Considerations

- All configurations include security hardening
- Sensitive information should be properly secured
- Regular security updates are recommended
- Monitor for unusual traffic patterns

## 📞 Support

If you encounter issues:
1. Check the troubleshooting section above
2. Review Magento logs in `var/log/`
3. Check web server error logs
4. Verify all services are running properly

## 📄 License

These configurations are provided as-is for educational and optimization purposes. Test thoroughly in a staging environment before applying to production.

---

**Last Updated**: $(date)
**Magento Version**: 2.4.7-p2
**PHP Version**: 8.1 (recommended)