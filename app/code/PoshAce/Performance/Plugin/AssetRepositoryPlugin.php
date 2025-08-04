<?php
/**
 * Asset Repository Plugin for Performance Optimizations
 *
 * @category  PoshAce
 * @package   PoshAce_Performance
 * @author    PoshAce
 * @copyright Copyright (c) 2024 PoshAce
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace PoshAce\Performance\Plugin;

use Magento\Framework\View\Asset\Repository;
use Magento\Framework\View\Asset\File;

class AssetRepositoryPlugin
{
    /**
     * Optimize asset URLs for better caching
     *
     * @param Repository $subject
     * @param callable $proceed
     * @param string $fileId
     * @param array $params
     * @return File
     */
    public function aroundCreateAsset(Repository $subject, callable $proceed, $fileId, $params = [])
    {
        $asset = $proceed($fileId, $params);
        
        // Add version parameter for better cache busting
        if ($asset instanceof File) {
            $this->addVersionToAsset($asset);
        }
        
        return $asset;
    }
    
    /**
     * Add version parameter to asset URL
     *
     * @param File $asset
     * @return void
     */
    private function addVersionToAsset(File $asset)
    {
        $url = $asset->getUrl();
        
        // Add version parameter if not already present
        if (strpos($url, '?') === false) {
            $url .= '?v=' . $this->getAssetVersion();
        } elseif (strpos($url, 'v=') === false) {
            $url .= '&v=' . $this->getAssetVersion();
        }
        
        // Use reflection to update the URL
        $reflection = new \ReflectionClass($asset);
        $property = $reflection->getProperty('url');
        $property->setAccessible(true);
        $property->setValue($asset, $url);
    }
    
    /**
     * Get asset version for cache busting
     *
     * @return string
     */
    private function getAssetVersion()
    {
        // Use deployment timestamp or a fixed version
        return '1.0.0';
    }
}