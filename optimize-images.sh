#!/bin/bash

# Magento 2 Image Optimization Script
# This script optimizes images in the media directory

echo "============================================"
echo "Magento 2 Image Optimization Script"
echo "============================================"

# Set variables
MEDIA_DIR="/workspace/pub/media"
LOG_FILE="/workspace/var/log/image-optimization.log"

# Check if required tools are installed
check_tool() {
    if ! command -v $1 &> /dev/null; then
        echo "$1 is not installed. Installing..."
        return 1
    else
        echo "$1 is already installed."
        return 0
    fi
}

# Install image optimization tools
install_tools() {
    echo "Installing image optimization tools..."
    
    # Update package list
    apt-get update -y
    
    # Install optimization tools
    apt-get install -y jpegoptim optipng pngquant webp imagemagick
    
    echo "Tools installed successfully."
}

# Function to optimize JPEG images
optimize_jpeg() {
    echo "Optimizing JPEG images..."
    find "$MEDIA_DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" \) -print0 | while IFS= read -r -d '' file; do
        echo "Processing: $file" >> "$LOG_FILE"
        jpegoptim --strip-all --max=85 "$file" >> "$LOG_FILE" 2>&1
    done
}

# Function to optimize PNG images
optimize_png() {
    echo "Optimizing PNG images..."
    find "$MEDIA_DIR" -type f -iname "*.png" -print0 | while IFS= read -r -d '' file; do
        echo "Processing: $file" >> "$LOG_FILE"
        # First use pngquant for lossy compression
        pngquant --force --quality=65-80 --ext .png "$file" >> "$LOG_FILE" 2>&1
        # Then use optipng for lossless compression
        optipng -o2 "$file" >> "$LOG_FILE" 2>&1
    done
}

# Function to convert images to WebP
create_webp() {
    echo "Creating WebP versions..."
    find "$MEDIA_DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) -print0 | while IFS= read -r -d '' file; do
        webp_file="${file%.*}.webp"
        if [ ! -f "$webp_file" ]; then
            echo "Creating WebP: $webp_file" >> "$LOG_FILE"
            cwebp -q 80 "$file" -o "$webp_file" >> "$LOG_FILE" 2>&1
        fi
    done
}

# Function to resize large images
resize_large_images() {
    echo "Resizing large images..."
    MAX_WIDTH=2000
    MAX_HEIGHT=2000
    
    find "$MEDIA_DIR" -type f \( -iname "*.jpg" -o -iname "*.jpeg" -o -iname "*.png" \) -print0 | while IFS= read -r -d '' file; do
        dimensions=$(identify -format "%wx%h" "$file" 2>/dev/null)
        if [ $? -eq 0 ]; then
            width=$(echo $dimensions | cut -d'x' -f1)
            height=$(echo $dimensions | cut -d'x' -f2)
            
            if [ "$width" -gt "$MAX_WIDTH" ] || [ "$height" -gt "$MAX_HEIGHT" ]; then
                echo "Resizing: $file (${width}x${height})" >> "$LOG_FILE"
                convert "$file" -resize "${MAX_WIDTH}x${MAX_HEIGHT}>" "$file" >> "$LOG_FILE" 2>&1
            fi
        fi
    done
}

# Main execution
echo "Starting image optimization process..."
echo "Log file: $LOG_FILE"
echo "" > "$LOG_FILE"

# Check and install tools if needed
for tool in jpegoptim optipng pngquant cwebp convert; do
    if ! check_tool $tool; then
        install_tools
        break
    fi
done

# Create backup directory
BACKUP_DIR="$MEDIA_DIR/../media_backup_$(date +%Y%m%d_%H%M%S)"
echo "Creating backup at: $BACKUP_DIR"
cp -r "$MEDIA_DIR" "$BACKUP_DIR"

# Run optimization
echo ""
echo "1. Resizing large images..."
resize_large_images

echo ""
echo "2. Optimizing JPEG images..."
optimize_jpeg

echo ""
echo "3. Optimizing PNG images..."
optimize_png

echo ""
echo "4. Creating WebP versions..."
create_webp

# Calculate space saved
ORIGINAL_SIZE=$(du -sh "$BACKUP_DIR" | cut -f1)
NEW_SIZE=$(du -sh "$MEDIA_DIR" | cut -f1)

echo ""
echo "============================================"
echo "Image Optimization Complete!"
echo "============================================"
echo "Original size: $ORIGINAL_SIZE"
echo "New size: $NEW_SIZE"
echo "Backup location: $BACKUP_DIR"
echo ""
echo "To use WebP images, configure your Magento theme to serve WebP"
echo "when supported by the browser using <picture> elements or nginx rules."
echo ""