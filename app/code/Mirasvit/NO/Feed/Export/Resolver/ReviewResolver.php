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

namespace Mirasvit\Feed\Export\Resolver;

use Magento\Review\Model\Review;
use Magento\Catalog\Model\Product;

class ReviewResolver extends AbstractResolver
{

    public function getAttributes(): array
    {
        return [];
    }

    public function getProduct(Review $review): Product
    {
        $product = $review->getProductCollection()
            ->addFieldToFilter('entity_id', $review->getEntityPkValue())
            ->getFirstItem();

        if (!$product->getId()) {
            $product->load($review->getEntityPkValue());
        }

        $product->setStoreId($this->getStoreId());

        return $product;
    }

    public function getRating(Review $review): float
    {
        $product = $this->getProduct($review);
        $review->getEntitySummary($product);
        $ratingSummary = $product->getRatingSummary()->getRatingSummary();

        if ($ratingSummary > 0) {
            return ($ratingSummary / 100) * 5;
        }

        return 5;
    }

    public function getReviewsCount(Review $review): int
    {
        $product = $this->getProduct($review);
        $review->getEntitySummary($product);
        $reviewsCount = $product->getRatingSummary()->getReviewsCount();

        return $reviewsCount;
    }

    public function getStoreId(): int
    {
        return $this->getFeed() ? (int)$this->getFeed()->getStore()->getId() : 0;
    }
}
