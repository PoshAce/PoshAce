<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.4.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */


declare(strict_types=1);

namespace Mirasvit\Feed\Export\Filter;

use Magento\Framework\UrlInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Store\Model\Store;

class UrlFilter
{
    protected $storeManager;

    protected $filesystem;

    public function __construct(
        StoreManagerInterface $storeManager,
        Filesystem            $filesystem
    ) {
        $this->storeManager = $storeManager;
        $this->filesystem   = $filesystem;
    }

    /**
     * Secure product URL
     *
     * @param string $input
     *
     * @return string
     */
    public function secure($input): string
    {
        return str_replace('http://', 'https://', $input);
    }

    /**
     * Usecure product URL
     *
     * @param string $input
     *
     * @return string
     */
    public function unsecure($input): string
    {
        return str_replace('https://', 'http://', $input);
    }

    /**
     * Secure Media URL
     *
     * @param string $input
     *
     * @return string
     */
    public function mediaSecure($input): string
    {
        return $this->replaceMediaUrl($input, true);
    }

    /**
     * Unsecure Media URL
     *
     * @param string $input
     *
     * @return string
     */
    public function mediaUnsecure($input): string
    {
        return $this->replaceMediaUrl($input, false);
    }

    /**
     * Replace media Url
     *
     * @param string $url
     * @param bool   $secure
     *
     * @return string
     */
    protected function replaceMediaUrl($url, $secure): string
    {
        $path = $secure ? Store::XML_PATH_SECURE_BASE_MEDIA_URL : Store::XML_PATH_UNSECURE_BASE_MEDIA_URL;

        /** @var Store $store */
        $store     = $this->storeManager->getStore();
        $staticUrl = $store->getConfig($path);

        $baseUrl = $store->getBaseUrl(UrlInterface::URL_TYPE_WEB, false)
            . $this->filesystem->getUri(DirectoryList::MEDIA) . '/';

        if (!$staticUrl) {
            return $url;
        } else {
            return str_replace($baseUrl, $staticUrl, $url);
        }
    }
}
