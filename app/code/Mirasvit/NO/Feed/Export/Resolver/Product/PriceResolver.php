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
use Magento\Tax\Model\Calculation as TaxCalculation;
use Magento\Catalog\Helper\Data as TaxHelper;

class PriceResolver extends AbstractResolver
{

    private $taxCalculation;

    private $taxHelper;

    public function __construct(
        TaxCalculation $taxCalculation,
        TaxHelper      $taxHelper
    ) {
        $this->taxCalculation = $taxCalculation;
        $this->taxHelper      = $taxHelper;
    }

    public function getPrice(Product $product): float
    {
        return (float)$product->getPrice();
    }

    public function getRegularPrice(Product $product): float
    {
        return (float)$product->getPriceInfo()->getPrice('regular_price')->getValue();
    }

    public function getSpecialPrice(Product $product)
    {
        $specialPriceInfo = $product->getPriceInfo()->getPrice('special_price');
        $specialPrice     = $specialPriceInfo->getValue();
        $today            = time();
        if ($specialPrice) {
            $specialFromDate = (string)$specialPriceInfo->getSpecialFromDate();
            $specialToDate   = (string)$specialPriceInfo->getSpecialToDate();
            if ($today >= strtotime($specialFromDate) && $today <= strtotime($specialToDate) ||
                $today >= strtotime($specialFromDate) && $specialToDate == "") {
                return (float)$specialPrice;
            }
        }
    }

    public function getFinalPrice(Product $product): float
    {
        return (float)$product->getPriceInfo()->getPrice('final_price')->getValue();
    }

    public function getFinalPriceTax(Product $product): float
    {
        return $this->taxHelper->getTaxPrice($product, $this->getFinalPrice($product), true);
    }

    public function getTaxRate(Product $product): float
    {
        if ($this->getFeed()) {
            $storeId = $this->getFeed()->getStoreId();
        } else {
            $storeId = 0;
        }

        $request = $this->taxCalculation->getRateRequest(null, null, null, $storeId);
        $request->setData('product_class_id', $product->getTaxClassId());

        return $this->taxCalculation->getRate($request);
    }
}
