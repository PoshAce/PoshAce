# Poshace Website Performance Optimization

This repository contains comprehensive performance optimization tools and configurations for the Poshace website (https://poshace.com/).

## 🚀 Quick Start

### 1. Install Dependencies
```bash
npm install
```

### 2. Run Optimization Scripts
```bash
# Extract critical CSS
npm run extract-critical

# Optimize images
npm run optimize-images

# Test performance
npm run test-performance
```

## 📊 Current Performance Issues

Based on analysis of https://poshace.com/, the following issues were identified:

- **15+ CSS files** loaded separately (should be bundled)
- **Cache headers** preventing browser caching
- **32 CSS/JS files** total (too many HTTP requests)
- **No Gzip compression** enabled
- **No CDN** for static assets
- **Unoptimized images** (no WebP format)

## 🛠️ Optimization Tools

### 1. Critical CSS Extractor
Extracts above-the-fold CSS to inline in the `<head>` section.

```bash
node critical_css_extractor.js
```

**Output:**
- `critical.css` - Extracted critical CSS
- `critical-css-snippet.html` - HTML snippet to add to your site
- `async-css-loader.html` - Async CSS loading script

### 2. Image Optimizer
Optimizes images and converts them to WebP format.

```bash
node image_optimizer.js
```

**Features:**
- Compresses JPG/PNG images
- Converts to WebP format
- Generates responsive image sizes
- Creates HTML snippets for implementation

### 3. Magento Optimization Script
Runs all Magento performance optimizations.

```bash
chmod +x magento_optimization_commands.sh
./magento_optimization_commands.sh
```

## 🔧 Implementation Guide

### Phase 1: Server Configuration (Immediate)

#### 1. Update .htaccess
Replace your current `.htaccess` file with the optimized version:

```bash
cp .htaccess_optimization /path/to/your/magento/.htaccess
```

**Key improvements:**
- Gzip compression for all text-based files
- Browser caching for static assets (1 year)
- Security headers
- WebP image serving
- HTTP/2 optimization

#### 2. Run Magento Optimizations
```bash
cd /path/to/your/magento
./magento_optimization_commands.sh
```

### Phase 2: Asset Optimization (1 week)

#### 1. Extract Critical CSS
```bash
npm run extract-critical
```

Add the generated critical CSS snippet to your Magento theme's `<head>` section.

#### 2. Optimize Images
```bash
npm run optimize-images
```

Replace your current images with the optimized versions from the `optimized-images/` directory.

#### 3. Implement WebP Images
Use the generated HTML snippets for WebP images with fallbacks.

### Phase 3: Advanced Optimizations (2-3 weeks)

#### 1. Set up CDN
Recommended CDN providers:
- **Cloudflare** (Free tier available)
- **AWS CloudFront**
- **Bunny CDN**

#### 2. Configure Redis
Add to your `app/etc/env.php`:

```php
'cache' => [
    'frontend' => [
        'default' => [
            'backend' => 'Cm_Cache_Backend_Redis',
            'backend_options' => [
                'server' => '127.0.0.1',
                'port' => '6379',
                'database' => '0',
            ]
        ]
    ]
],
'session' => [
    'save' => 'redis',
    'redis' => [
        'host' => '127.0.0.1',
        'port' => '6379',
        'database' => '1',
    ]
]
```

#### 3. Enable Full Page Cache
In Magento Admin:
1. Go to **Stores > Configuration > Advanced > System > Full Page Cache**
2. Set **Caching Application** to **Built-in Application**
3. Set **TTL for public content** to **86400** (24 hours)

## 📈 Expected Performance Improvements

| Metric | Current | Target | Improvement |
|--------|---------|--------|-------------|
| Page Load Time | ~4-6s | ~2-3s | 40-60% |
| First Contentful Paint | ~2-3s | ~1-1.5s | 30-50% |
| Largest Contentful Paint | ~3-4s | ~1.5-2s | 35-55% |
| Cumulative Layout Shift | ~0.2-0.3 | ~0.05-0.1 | 70-90% |
| Google PageSpeed Score | ~60-70 | ~85-95 | +25-35 |

## 🔍 Performance Monitoring

### Automated Testing
```bash
# Run Lighthouse audit
npm run test-performance

# Monitor performance over time
npm run monitor
```

### Manual Testing Tools
- **Google PageSpeed Insights**: https://pagespeed.web.dev/
- **GTmetrix**: https://gtmetrix.com/
- **WebPageTest**: https://www.webpagetest.org/

### Key Metrics to Track
- **Core Web Vitals**
  - Largest Contentful Paint (LCP) < 2.5s
  - First Input Delay (FID) < 100ms
  - Cumulative Layout Shift (CLS) < 0.1

- **Performance Metrics**
  - First Contentful Paint (FCP) < 1.8s
  - Speed Index < 3.4s
  - Time to Interactive (TTI) < 3.8s

## 🚨 Troubleshooting

### Common Issues

#### 1. Cache Not Working
- Check if `.htaccess` is properly configured
- Verify Apache modules are enabled (`mod_expires`, `mod_deflate`)
- Clear browser cache and test

#### 2. Images Not Loading
- Ensure WebP images are generated
- Check file permissions on optimized images
- Verify CDN configuration

#### 3. CSS Not Loading
- Check critical CSS implementation
- Verify async CSS loading script
- Clear Magento cache

### Debug Commands
```bash
# Check Apache modules
apache2ctl -M | grep -E "(expires|deflate|headers)"

# Test Gzip compression
curl -H "Accept-Encoding: gzip" -I https://poshace.com/

# Check cache headers
curl -I https://poshace.com/static/version*/css/styles-m.min.css
```

## 📚 Additional Resources

### Magento Performance
- [Magento Performance Best Practices](https://developer.adobe.com/commerce/php/coding-standards/technical-guidelines/performance/)
- [Magento Cache Management](https://docs.magento.com/user-guide/system/cache-management.html)

### Web Performance
- [Web.dev Performance](https://web.dev/performance/)
- [Google PageSpeed Insights](https://developers.google.com/speed/pagespeed/insights/)

### Image Optimization
- [WebP Format Guide](https://developers.google.com/speed/webp)
- [Responsive Images](https://developer.mozilla.org/en-US/docs/Learn/HTML/Multimedia_and_embedding/Responsive_images)

## 🤝 Support

For questions or issues with the optimization process:

1. Check the troubleshooting section above
2. Review the generated reports in the output files
3. Test with performance monitoring tools
4. Contact your hosting provider for server-level optimizations

## 📄 License

MIT License - feel free to use and modify for your own projects.

---

**Last Updated**: August 2024  
**Website**: https://poshace.com/  
**Performance Target**: 90+ Google PageSpeed Score