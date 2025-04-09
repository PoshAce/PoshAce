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

namespace Mirasvit\Feed\Export\Resolver\Product\Type;

use Magento\Bundle\Model\Product\Type;
use Magento\Catalog\Model\Product;
use Mirasvit\Feed\Export\Resolver\ProductResolver;

class BundleResolver extends ProductResolver
{
    public function getAttributes(): array
    {
        return [];
    }

    public function getAssociatedProducts(Product $product): array
    {
        /** @var Type $type */
        $type = $product->getTypeInstance();

        return $type->getAssociatedProducts($type);
    }
}
