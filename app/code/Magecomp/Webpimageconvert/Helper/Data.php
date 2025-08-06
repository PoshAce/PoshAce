<?php

namespace Magecomp\Webpimageconvert\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Framework\App\Helper\Context;
use Magento\Framework\Filesystem;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Framework\App\Filesystem\DirectoryList;

class Data extends AbstractHelper
{
    protected $storeManager;

    protected $filesystem;

    protected $newFile;

    protected $ioFile;

    protected $imageQuality;


    public function __construct(
        Context $context,
        StoreManagerInterface $storeManager,
        Filesystem $filesystem,
        Filesystem\Io\File $ioFile
    )
    {
        $this->ioFile = $ioFile;
        $this->storeManager = $storeManager;
        $this->filesystem = $filesystem;
        parent::__construct($context);
    }

    /**
     * Convert Image to Webp Image
     *
     * @param $imageUrl
     * @return string
     */
    public function convert($imageUrl)
    {
        $webpUrl = $this->getWebpNameFromImage($imageUrl);
        $webpPath = $this->getImagePathFromUrl($webpUrl);
        $this->newFile = $webpPath;
        $folder = dirname($webpPath);
        $this->createFolderIfNotExist($folder);
        $imagePath = $this->getImagePathFromUrl($imageUrl);
        if (empty($imagePath)) {
            return $imageUrl;
        }

        if (!file_exists($imagePath)) {
            return $imageUrl;
        }

        if (!$this->isEnabled()) {
            return $imageUrl;
        }

        if (file_exists($webpPath)) {
            return $webpUrl;
        }

        $this->newFile = $this->convertToWebpViaGd($imagePath, $webpPath);
       $webpUrl = $this->getImageUrlFromPath($this->newFile);
        return $webpUrl;

    }

    /**
     * Method to convert an image to WebP using the GD method
     *
     * @param $imagePath
     * @param $webpPath
     *
     * @return bool
     */
    public function convertToWebpViaGd($imagePath, $webpPath)
    {
        if ($this->hasGdSupport() == false) {
            return $imagePath;
        }
        $imageData = file_get_contents($imagePath);

        try {
            $image = imagecreatefromstring($imageData);
            imagepalettetotruecolor($image);
        } catch (\Exception $ex) {
            return false;
        }

        imagewebp($image, $webpPath, $this->imageQuality());
        return $webpPath;

    }

    /**
     * @param null $store
     * @return bool
     */
    public function isEnabled($store = null)
    {
        return $this->scopeConfig->getValue('webpimage/general/enable', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
    }

    /**
     * @param null $store
     * @return bool
     */
    public function imageQuality($store = null)
    {
        if (!$this->imageQuality) {
            $this->imageQuality = $this->scopeConfig->getValue('webpimage/general/quality', \Magento\Store\Model\ScopeInterface::SCOPE_STORE, $store);
        }
        return $this->imageQuality;
    }

    /**
     * @return bool
     */
    public function hasGdSupport()
    {
        if (!function_exists('imagewebp')) {
            return false;
        }

        return true;
    }

    /**
     * Get the WebP path equivalent of an image path
     *
     * @param $image
     *
     * @return mixed
     */
    public function getWebpNameFromImage($image)
    {
        $image = preg_replace('/\.(png|jpg|jpeg)$/i', '.webp', $image);
        $image = str_replace('pub/media/', 'pub/media/webp_image/', $image);
        return $image;
    }

    /**
     * @return array
     */
    public function getSystemPaths()
    {
        $systemPaths = array(
            'pub' => array(
                'url' => $this->storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA),
                'path' => $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath())
        );
        return $systemPaths;
    }

    /**
     * @param string $imageUrl
     *
     * @return mixed
     */
    public function getImagePathFromUrl($imageUrl)
    {
        $systemPaths = $this->getSystemPaths();

        if (preg_match('/^http/', $imageUrl)) {
            foreach ($systemPaths as $systemPath) {
                if (strstr($imageUrl, $systemPath['url'])) {
                    return str_replace($systemPath['url'], $systemPath['path'], $imageUrl);
                }
            }
        }
        return false;
    }

    /**
     * @param string $imagePath
     *
     * @return mixed
     */
    public function getImageUrlFromPath($imagePath)
    {
        $systemPaths = $this->getSystemPaths();
        if (!preg_match('/^http/', $imagePath)) {
            foreach ($systemPaths as $systemPath) {
                if (strstr($imagePath, $systemPath['path'])) {
                    return str_replace($systemPath['path'], $systemPath['url'], $imagePath);
                }
            }
        }
        return false;
    }

    public function createFolderIfNotExist($path)
    {
        if (!is_dir($path)) {
            $ioAdapter = $this->ioFile;
            $ioAdapter->mkdir($path, 0775);
        }
    }

    /**
     * @param $folderPath
     *
     * @return bool|string
     */
    public function removeFolder($folderPath)
    {
        if (is_dir($folderPath)) {
            if (is_writable($folderPath)) {
                $ioAdapter = $this->ioFile;
                $ioAdapter->rmdir($folderPath, true);
            } else {
                return false;
            }
        } else {
            return 'nowebpFolder';
        }
    }

    public function clearWebp()
    {
        $webpFolder = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'webp_image';
        return $this->removeFolder($webpFolder) ;
    }

}