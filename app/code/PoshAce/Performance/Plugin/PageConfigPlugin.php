<?php
/**
 * Page Config Plugin for Performance Optimizations
 *
 * @category  PoshAce
 * @package   PoshAce_Performance
 * @author    PoshAce
 * @copyright Copyright (c) 2024 PoshAce
 * @license   https://opensource.org/licenses/MIT MIT License
 */

namespace PoshAce\Performance\Plugin;

use Magento\Framework\View\Page\Config as PageConfig;

class PageConfigPlugin
{
    /**
     * Add performance optimizations to page head
     *
     * @param PageConfig $subject
     * @param callable $proceed
     * @return PageConfig
     */
    public function aroundGetIncludes(PageConfig $subject, callable $proceed)
    {
        $result = $proceed();
        
        // Add font preloading for better font display performance
        $this->addFontPreloading($subject);
        
        // Add resource hints for critical resources
        $this->addResourceHints($subject);
        
        return $result;
    }
    
    /**
     * Add font preloading
     *
     * @param PageConfig $config
     * @return void
     */
    private function addFontPreloading(PageConfig $config)
    {
        // Add preload for critical fonts
        $config->addRemotePageAsset(
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
            'css',
            ['rel' => 'preload', 'as' => 'style', 'onload' => "this.onload=null;this.rel='stylesheet'"]
        );
        
        // Add font display swap for better performance
        $config->addRemotePageAsset(
            'https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap',
            'css',
            ['media' => 'print', 'onload' => "this.media='all'"]
        );
    }
    
    /**
     * Add resource hints for critical resources
     *
     * @param PageConfig $config
     * @return void
     */
    private function addResourceHints(PageConfig $config)
    {
        // DNS prefetch for external domains
        $config->addRemotePageAsset(
            '//fonts.googleapis.com',
            'link',
            ['rel' => 'dns-prefetch']
        );
        
        $config->addRemotePageAsset(
            '//fonts.gstatic.com',
            'link',
            ['rel' => 'dns-prefetch']
        );
        
        // Preconnect for critical resources
        $config->addRemotePageAsset(
            'https://fonts.googleapis.com',
            'link',
            ['rel' => 'preconnect', 'crossorigin' => '']
        );
        
        $config->addRemotePageAsset(
            'https://fonts.gstatic.com',
            'link',
            ['rel' => 'preconnect', 'crossorigin' => '']
        );
    }
}