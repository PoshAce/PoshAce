const puppeteer = require('puppeteer');
const fs = require('fs');
const path = require('path');

// Critical CSS Extractor for Poshace Website
// This script extracts above-the-fold CSS to inline in the head

async function extractCriticalCSS() {
    console.log('🎯 Starting Critical CSS extraction for Poshace...');
    
    const browser = await puppeteer.launch({
        headless: true,
        args: ['--no-sandbox', '--disable-setuid-sandbox']
    });
    
    try {
        const page = await browser.newPage();
        
        // Set viewport to common mobile size
        await page.setViewport({ width: 375, height: 667 });
        
        // Navigate to the homepage
        console.log('📱 Loading homepage...');
        await page.goto('https://poshace.com/', {
            waitUntil: 'networkidle2',
            timeout: 30000
        });
        
        // Extract critical CSS
        console.log('🔍 Extracting critical CSS...');
        const criticalCSS = await page.evaluate(() => {
            const styleSheets = Array.from(document.styleSheets);
            let criticalCSS = '';
            
            styleSheets.forEach(sheet => {
                try {
                    const rules = Array.from(sheet.cssRules || sheet.rules);
                    rules.forEach(rule => {
                        if (rule.cssText) {
                            criticalCSS += rule.cssText + '\n';
                        }
                    });
                } catch (e) {
                    // Skip external stylesheets that can't be accessed
                }
            });
            
            return criticalCSS;
        });
        
        // Save critical CSS
        const outputPath = path.join(__dirname, 'critical.css');
        fs.writeFileSync(outputPath, criticalCSS);
        console.log(`✅ Critical CSS saved to: ${outputPath}`);
        
        // Generate HTML snippet for inline critical CSS
        const htmlSnippet = `<!-- Critical CSS for Poshace -->
<style id="critical-css">
${criticalCSS}
</style>`;
        
        const htmlPath = path.join(__dirname, 'critical-css-snippet.html');
        fs.writeFileSync(htmlPath, htmlSnippet);
        console.log(`✅ HTML snippet saved to: ${htmlPath}`);
        
        // Extract non-critical CSS links
        const nonCriticalCSS = await page.evaluate(() => {
            const links = Array.from(document.querySelectorAll('link[rel="stylesheet"]'));
            return links.map(link => link.href).filter(href => 
                href.includes('poshace.com') && 
                !href.includes('critical')
            );
        });
        
        // Generate async CSS loading snippet
        const asyncCSSSnippet = `<!-- Async CSS Loading for Poshace -->
<script>
(function() {
    const links = ${JSON.stringify(nonCriticalCSS)};
    links.forEach(href => {
        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href = href;
        link.media = 'print';
        link.onload = function() {
            this.media = 'all';
        };
        document.head.appendChild(link);
    });
})();
</script>`;
        
        const asyncPath = path.join(__dirname, 'async-css-loader.html');
        fs.writeFileSync(asyncPath, asyncCSSSnippet);
        console.log(`✅ Async CSS loader saved to: ${asyncPath}`);
        
        console.log('\n📊 Summary:');
        console.log(`- Critical CSS size: ${(criticalCSS.length / 1024).toFixed(2)} KB`);
        console.log(`- Non-critical CSS files: ${nonCriticalCSS.length}`);
        console.log('\n🔧 Implementation:');
        console.log('1. Add the critical CSS snippet to your <head> section');
        console.log('2. Add the async CSS loader after the critical CSS');
        console.log('3. Remove the original CSS link tags');
        
    } catch (error) {
        console.error('❌ Error extracting critical CSS:', error);
    } finally {
        await browser.close();
    }
}

// Run the extraction
extractCriticalCSS().catch(console.error);

// Additional utility functions
function optimizeCSS(css) {
    // Remove comments
    css = css.replace(/\/\*[\s\S]*?\*\//g, '');
    
    // Remove unnecessary whitespace
    css = css.replace(/\s+/g, ' ');
    css = css.replace(/;\s*}/g, '}');
    css = css.replace(/{\s*/g, '{');
    css = css.replace(/\s*}/g, '}');
    
    // Remove empty rules
    css = css.replace(/[^}]+{\s*}/g, '');
    
    return css.trim();
}

function generateWebpackConfig() {
    const webpackConfig = `const MiniCssExtractPlugin = require('mini-css-extract-plugin');
const OptimizeCSSAssetsPlugin = require('optimize-css-assets-webpack-plugin');
const TerserPlugin = require('terser-webpack-plugin');

module.exports = {
    mode: 'production',
    entry: {
        main: './src/js/main.js',
        critical: './src/css/critical.css'
    },
    output: {
        filename: '[name].[contenthash].js',
        path: __dirname + '/dist'
    },
    optimization: {
        minimizer: [
            new TerserPlugin(),
            new OptimizeCSSAssetsPlugin()
        ],
        splitChunks: {
            cacheGroups: {
                styles: {
                    name: 'styles',
                    test: /\\.css$/,
                    chunks: 'all',
                    enforce: true
                }
            }
        }
    },
    plugins: [
        new MiniCssExtractPlugin({
            filename: '[name].[contenthash].css'
        })
    ],
    module: {
        rules: [
            {
                test: /\\.css$/,
                use: [MiniCssExtractPlugin.loader, 'css-loader', 'postcss-loader']
            }
        ]
    }
};`;
    
    fs.writeFileSync('webpack.config.js', webpackConfig);
    console.log('✅ Webpack config generated');
}

// Export functions for use in other scripts
module.exports = {
    extractCriticalCSS,
    optimizeCSS,
    generateWebpackConfig
};