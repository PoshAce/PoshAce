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


namespace Mirasvit\Feed\Export\Resolver\Product;

use Magento\Catalog\Model\Product;
use Magento\Framework\Data\Collection;
use Magento\Framework\DataObject;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Tax\Model\Calculation as TaxCalculation;
use Magento\Framework\UrlInterface;
use Traversable;

class ImageResolver extends AbstractResolver
{

    private $storeManager;

    public function __construct(
        StoreManagerInterface $storeManager
    ) {
        $this->storeManager = $storeManager;
    }

    public function getImage(Product $product): string
    {
        if ($product->getImage()) {
            return $this->getImageUrl($product, $product->getImage());
        }

        return '';
    }

    public function getThumbnail(Product $product): string
    {
        if ($product->getThumbnail()) {
            return $this->getImageUrl($product, $product->getThumbnail());
        }

        return '';
    }

    public function getSmallImage(Product $product): string
    {
        if ($product->getSmallImage()) {
            return $this->getImageUrl($product, $product->getSmallImage());
        }

        return '';
    }

    public function getGallery(Product $product, array $args = []): array
    {
        $gallery = [];

        /** @var Collection|array $galleryImages */
        $galleryImages = $product->getMediaGalleryImages();
        if (is_array($galleryImages) || $galleryImages instanceof Traversable) {
            foreach ($galleryImages as $galleryImage) {
                /** @var DataObject $galleryImage */
                $gallery[] = $this->getImageUrl($product, $galleryImage->getData('file'));
            }
        }

        if (isset($args[0]) && is_numeric($args[0])) {
            return array_slice($gallery, 0, $args[0]);
        }

        return $gallery;
    }

    public function getImages(Product $product): string
    {
        $galleryCollection = $this->getGallery($product);

        return implode(",", $galleryCollection);
    }

    protected function getImageUrl(Product $product, string $file): string
    {
        $baseUrl = $this->storeManager->getStore()->getBaseUrl(UrlInterface::URL_TYPE_MEDIA, false);
        $baseUrl = rtrim($baseUrl, '/');

        $file = ltrim(str_replace('\\', '/', $file), '/');

        $url = $baseUrl . '/' . $product->getMediaConfig()->getBaseMediaUrlAddition() . '/' . $file;

        return $url;
    }
}
