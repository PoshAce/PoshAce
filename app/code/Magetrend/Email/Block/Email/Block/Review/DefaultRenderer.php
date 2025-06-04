<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Block\Email\Block\Review;

class DefaultRenderer extends \Magetrend\Email\Block\Email\Block\Sales\Product\DefaultRenderer
{
    protected $productCache = [];

    public function getReviewLink($item)
    {
        return $this->getLayout()
            ->createBlock(\Magetrend\Review\Block\Email\Items\DefaultRenderer::class)
            ->getReviewLink($item);
    }

    public function getProduct()
    {
        $productId = $this->getItem()->getProductId();
        if (!isset($this->productCache[$productId])) {
            $this->productCache[$productId] = $this->productFactory->create()->load($productId);
        }

        return $this->productCache[$productId];
    }
}