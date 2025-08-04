<?php
/**
 * Image Optimization Helper
 *
 * @category  PoshAce
 * @package   PoshAce_Performance
 * @author    PoshAce
 * @copyright Copyright (c) 2024 PoshAce
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace PoshAce\Performance\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Store\Model\ScopeInterface;

class ImageOptimizer extends AbstractHelper
{
    /**
     * Configuration path for image optimization
     */
    const XML_PATH_IMAGE_OPTIMIZATION = 'poshace_performance/images/';

    /**
     * Check if WebP is enabled
     *
     * @return bool
     */
    public function isWebpEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IMAGE_OPTIMIZATION . 'enable_webp',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Check if lazy loading is enabled
     *
     * @return bool
     */
    public function isLazyLoadingEnabled()
    {
        return $this->scopeConfig->isSetFlag(
            self::XML_PATH_IMAGE_OPTIMIZATION . 'enable_lazy_loading',
            ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * Get image quality setting
     *
     * @return int
     */
    public function getImageQuality()
    {
        return (int) $this->scopeConfig->getValue(
            self::XML_PATH_IMAGE_OPTIMIZATION . 'quality',
            ScopeInterface::SCOPE_STORE
        ) ?: 85;
    }

    /**
     * Convert image to WebP format
     *
     * @param string $sourcePath
     * @param string $destinationPath
     * @return bool
     */
    public function convertToWebP($sourcePath, $destinationPath)
    {
        if (!$this->isWebpEnabled()) {
            return false;
        }

        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $quality = $this->getImageQuality();

        switch ($imageInfo[2]) {
            case IMAGETYPE_JPEG:
                $image = imagecreatefromjpeg($sourcePath);
                break;
            case IMAGETYPE_PNG:
                $image = imagecreatefrompng($sourcePath);
                // Preserve transparency
                imagepalettetotruecolor($image);
                imagealphablending($image, true);
                imagesavealpha($image, true);
                break;
            case IMAGETYPE_GIF:
                $image = imagecreatefromgif($sourcePath);
                break;
            default:
                return false;
        }

        if (!$image) {
            return false;
        }

        $result = imagewebp($image, $destinationPath, $quality);
        imagedestroy($image);

        return $result;
    }

    /**
     * Generate responsive image sizes
     *
     * @param string $sourcePath
     * @param array $sizes
     * @return array
     */
    public function generateResponsiveImages($sourcePath, $sizes = [])
    {
        if (empty($sizes)) {
            $sizes = [320, 640, 768, 1024, 1200];
        }

        $responsiveImages = [];
        $imageInfo = getimagesize($sourcePath);
        
        if (!$imageInfo) {
            return $responsiveImages;
        }

        $originalWidth = $imageInfo[0];
        $originalHeight = $imageInfo[1];

        foreach ($sizes as $width) {
            if ($width <= $originalWidth) {
                $height = ($width / $originalWidth) * $originalHeight;
                $resizedPath = $this->resizeImage($sourcePath, $width, $height);
                
                if ($resizedPath) {
                    $responsiveImages[$width] = $resizedPath;
                }
            }
        }

        return $responsiveImages;
    }

    /**
     * Resize image to specified dimensions
     *
     * @param string $sourcePath
     * @param int $width
     * @param int $height
     * @return string|false
     */
    private function resizeImage($sourcePath, $width, $height)
    {
        $imageInfo = getimagesize($sourcePath);
        if (!$imageInfo) {
            return false;
        }

        $sourceImage = $this->createImageFromFile($sourcePath, $imageInfo[2]);
        if (!$sourceImage) {
            return false;
        }

        $resizedImage = imagecreatetruecolor($width, $height);
        
        // Preserve transparency for PNG images
        if ($imageInfo[2] === IMAGETYPE_PNG) {
            imagealphablending($resizedImage, false);
            imagesavealpha($resizedImage, true);
            $transparent = imagecolorallocatealpha($resizedImage, 255, 255, 255, 127);
            imagefilledrectangle($resizedImage, 0, 0, $width, $height, $transparent);
        }

        imagecopyresampled(
            $resizedImage,
            $sourceImage,
            0, 0, 0, 0,
            $width,
            $height,
            $imageInfo[0],
            $imageInfo[1]
        );

        $destinationPath = $this->generateResizedPath($sourcePath, $width, $height);
        $this->saveImage($resizedImage, $destinationPath, $imageInfo[2]);

        imagedestroy($sourceImage);
        imagedestroy($resizedImage);

        return $destinationPath;
    }

    /**
     * Create image resource from file
     *
     * @param string $path
     * @param int $type
     * @return resource|false
     */
    private function createImageFromFile($path, $type)
    {
        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagecreatefromjpeg($path);
            case IMAGETYPE_PNG:
                return imagecreatefrompng($path);
            case IMAGETYPE_GIF:
                return imagecreatefromgif($path);
            default:
                return false;
        }
    }

    /**
     * Save image to file
     *
     * @param resource $image
     * @param string $path
     * @param int $type
     * @return bool
     */
    private function saveImage($image, $path, $type)
    {
        $dir = dirname($path);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        switch ($type) {
            case IMAGETYPE_JPEG:
                return imagejpeg($image, $path, $this->getImageQuality());
            case IMAGETYPE_PNG:
                return imagepng($image, $path, 9);
            case IMAGETYPE_GIF:
                return imagegif($image, $path);
            default:
                return false;
        }
    }

    /**
     * Generate path for resized image
     *
     * @param string $originalPath
     * @param int $width
     * @param int $height
     * @return string
     */
    private function generateResizedPath($originalPath, $width, $height)
    {
        $pathInfo = pathinfo($originalPath);
        return $pathInfo['dirname'] . '/' . $pathInfo['filename'] . '-' . $width . 'x' . $height . '.' . $pathInfo['extension'];
    }
}