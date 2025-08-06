<?php

namespace Magecomp\Webpimageconvert\Plugin;

use Magento\Framework\View\LayoutInterface;
use Magecomp\Webpimageconvert\Block\Picture;

class ReplaceImageTag
{
    protected $helper;

    protected $storeManager;

    public function __construct(
        \Magecomp\Webpimageconvert\Helper\Data $helper,
        \Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->helper = $helper;
        $this->storeManager = $storeManager;
    }


    public function afterGetOutput(LayoutInterface $layout, $output)
    {
        if (!$this->helper->isEnabled()) {
            return $output;
        }

        $regex = '/<img([^<]+\s|\s)src=(\"|' . "\')([^<]+\.(png|jpg|jpeg))[^<]+>(?!(<\/pic|\s*<\/pic))/mi";
        if (preg_match_all($regex, $output, $images, PREG_OFFSET_CAPTURE) === false) {
            return $output;
        }
        
        $accumulatedChange = 0;
        $mediaUrl = $this ->storeManager-> getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_MEDIA );
        
        foreach ($images[0] as $index => $image)
        {
            $offset = $image[1] + $accumulatedChange;
            $htmlTag = $images[0][$index][0];
            $imageUrl = $images[3][$index][0];

            /**
             * Skip when image is not from same server
             */
            if (strpos($imageUrl, $mediaUrl) === false) {
                continue;
            }

            $pictureTag = $this->convertImage($imageUrl, $htmlTag, $layout);

            if (!$pictureTag) {
                continue;
            }

            $output = substr_replace($output, $pictureTag, $offset, strlen($htmlTag));
            $accumulatedChange = $accumulatedChange + (strlen($pictureTag) - strlen($htmlTag));
        }
        return $output;
    }

    /**
     * Get picture tag format
     *
     * @param LayoutInterface $layout
     * @return Picture
     */
    private function getPicture(LayoutInterface $layout)
    {
        $block = $layout->createBlock(Picture::class);
        return $block;
    }

    private function convertImage($imageUrl, $htmlTag, $layout)
    {
        $webpUrl = $this->helper->convert($imageUrl);

        /**
         * Skip when extension can not convert the image
         */
        if ($webpUrl === $imageUrl) {
            return false;
        }
            $pictureTag = $this->getPicture($layout)
                ->setOriginalImage($imageUrl)
                ->setWebpImage($webpUrl)
                ->setOriginalTag($htmlTag)
                ->toHtml();
        return $pictureTag;
    }
}