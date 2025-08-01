const sharp = require('sharp');
const fs = require('fs');
const path = require('path');
const imagemin = require('imagemin');
const imageminMozjpeg = require('imagemin-mozjpeg');
const imageminPngquant = require('imagemin-pngquant');
const imageminWebp = require('imagemin-webp');

// Image Optimizer for Poshace Website
// This script optimizes images and converts them to WebP format

class ImageOptimizer {
    constructor() {
        this.inputDir = './images';
        this.outputDir = './optimized-images';
        this.webpDir = './webp-images';
        this.supportedFormats = ['.jpg', '.jpeg', '.png', '.gif'];
    }

    async optimizeImages() {
        console.log('🖼️  Starting image optimization for Poshace...');
        
        // Create output directories
        this.createDirectories();
        
        try {
            // Get all images from input directory
            const images = this.getImageFiles();
            
            if (images.length === 0) {
                console.log('❌ No images found in input directory');
                return;
            }
            
            console.log(`📸 Found ${images.length} images to optimize`);
            
            // Process each image
            for (const image of images) {
                await this.processImage(image);
            }
            
            // Generate WebP versions
            await this.generateWebPVersions();
            
            // Generate responsive images
            await this.generateResponsiveImages();
            
            console.log('✅ Image optimization completed!');
            this.generateReport();
            
        } catch (error) {
            console.error('❌ Error optimizing images:', error);
        }
    }

    createDirectories() {
        const dirs = [this.outputDir, this.webpDir, './responsive-images'];
        dirs.forEach(dir => {
            if (!fs.existsSync(dir)) {
                fs.mkdirSync(dir, { recursive: true });
            }
        });
    }

    getImageFiles() {
        if (!fs.existsSync(this.inputDir)) {
            console.log(`📁 Creating input directory: ${this.inputDir}`);
            fs.mkdirSync(this.inputDir, { recursive: true });
            return [];
        }
        
        const files = fs.readdirSync(this.inputDir);
        return files.filter(file => {
            const ext = path.extname(file).toLowerCase();
            return this.supportedFormats.includes(ext);
        });
    }

    async processImage(filename) {
        const inputPath = path.join(this.inputDir, filename);
        const outputPath = path.join(this.outputDir, filename);
        
        console.log(`🔄 Processing: ${filename}`);
        
        try {
            const image = sharp(inputPath);
            const metadata = await image.metadata();
            
            // Optimize based on format
            const ext = path.extname(filename).toLowerCase();
            
            switch (ext) {
                case '.jpg':
                case '.jpeg':
                    await image
                        .jpeg({ quality: 85, progressive: true })
                        .toFile(outputPath);
                    break;
                    
                case '.png':
                    await image
                        .png({ quality: 85, progressive: true })
                        .toFile(outputPath);
                    break;
                    
                case '.gif':
                    await image
                        .gif()
                        .toFile(outputPath);
                    break;
            }
            
            // Get file sizes
            const originalSize = fs.statSync(inputPath).size;
            const optimizedSize = fs.statSync(outputPath).size;
            const savings = ((originalSize - optimizedSize) / originalSize * 100).toFixed(2);
            
            console.log(`   ✅ Optimized: ${filename} (${savings}% smaller)`);
            
        } catch (error) {
            console.error(`   ❌ Error processing ${filename}:`, error);
        }
    }

    async generateWebPVersions() {
        console.log('🔄 Generating WebP versions...');
        
        const images = this.getImageFiles();
        
        for (const image of images) {
            const inputPath = path.join(this.outputDir, image);
            const webpFilename = path.parse(image).name + '.webp';
            const webpPath = path.join(this.webpDir, webpFilename);
            
            try {
                await sharp(inputPath)
                    .webp({ quality: 85 })
                    .toFile(webpPath);
                    
                console.log(`   ✅ WebP created: ${webpFilename}`);
                
            } catch (error) {
                console.error(`   ❌ Error creating WebP for ${image}:`, error);
            }
        }
    }

    async generateResponsiveImages() {
        console.log('🔄 Generating responsive images...');
        
        const sizes = [
            { width: 300, suffix: 'small' },
            { width: 600, suffix: 'medium' },
            { width: 900, suffix: 'large' },
            { width: 1200, suffix: 'xlarge' }
        ];
        
        const images = this.getImageFiles();
        
        for (const image of images) {
            const inputPath = path.join(this.outputDir, image);
            const baseName = path.parse(image).name;
            const ext = path.parse(image).ext;
            
            for (const size of sizes) {
                const outputFilename = `${baseName}-${size.suffix}${ext}`;
                const outputPath = path.join('./responsive-images', outputFilename);
                
                try {
                    await sharp(inputPath)
                        .resize(size.width, null, { withoutEnlargement: true })
                        .toFile(outputPath);
                        
                    console.log(`   ✅ Responsive: ${outputFilename}`);
                    
                } catch (error) {
                    console.error(`   ❌ Error creating responsive ${outputFilename}:`, error);
                }
            }
        }
    }

    generateReport() {
        const report = {
            timestamp: new Date().toISOString(),
            originalImages: this.getImageFiles().length,
            optimizedImages: fs.readdirSync(this.outputDir).length,
            webpImages: fs.readdirSync(this.webpDir).length,
            responsiveImages: fs.readdirSync('./responsive-images').length,
            recommendations: [
                'Use WebP format with fallback for older browsers',
                'Implement lazy loading for images below the fold',
                'Use responsive images with srcset and sizes attributes',
                'Consider using a CDN for image delivery',
                'Implement progressive image loading'
            ]
        };
        
        fs.writeFileSync('image-optimization-report.json', JSON.stringify(report, null, 2));
        console.log('📊 Optimization report saved to: image-optimization-report.json');
    }

    generateHTMLSnippet() {
        const snippet = `<!-- Optimized Image Loading for Poshace -->
<picture>
    <source srcset="image.webp" type="image/webp">
    <source srcset="image.jpg" type="image/jpeg">
    <img src="image.jpg" alt="Description" loading="lazy">
</picture>

<!-- Responsive Images -->
<img srcset="image-small.jpg 300w,
             image-medium.jpg 600w,
             image-large.jpg 900w,
             image-xlarge.jpg 1200w"
     sizes="(max-width: 600px) 300px,
            (max-width: 900px) 600px,
            (max-width: 1200px) 900px,
            1200px"
     src="image-medium.jpg" 
     alt="Description"
     loading="lazy">`;
        
        fs.writeFileSync('optimized-image-snippets.html', snippet);
        console.log('✅ HTML snippets saved to: optimized-image-snippets.html');
    }
}

// Utility function to optimize images from URLs
async function optimizeImagesFromURLs(urls) {
    console.log('🌐 Optimizing images from URLs...');
    
    const axios = require('axios');
    const optimizer = new ImageOptimizer();
    
    for (const url of urls) {
        try {
            const response = await axios.get(url, { responseType: 'arraybuffer' });
            const filename = path.basename(url);
            const filepath = path.join(optimizer.inputDir, filename);
            
            fs.writeFileSync(filepath, response.data);
            console.log(`📥 Downloaded: ${filename}`);
            
        } catch (error) {
            console.error(`❌ Error downloading ${url}:`, error.message);
        }
    }
    
    await optimizer.optimizeImages();
}

// Example usage
if (require.main === module) {
    const optimizer = new ImageOptimizer();
    optimizer.optimizeImages().then(() => {
        optimizer.generateHTMLSnippet();
    });
}

module.exports = {
    ImageOptimizer,
    optimizeImagesFromURLs
};