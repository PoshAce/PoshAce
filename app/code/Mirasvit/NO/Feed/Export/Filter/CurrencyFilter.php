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

use Magento\Catalog\Model\Product;
use Magento\Directory\Helper\Data as DirectoryHelper;
use Magento\Store\Model\StoreManagerInterface;
use Mirasvit\Feed\Export\Context;
use Magento\Tax\Model\Calculation as TaxCalculation;

class CurrencyFilter
{
    protected $directoryHelper;

    protected $storeManager;

    protected $context;

    protected $taxCalculation;

    public function __construct(
        DirectoryHelper       $directoryHelper,
        StoreManagerInterface $storeManager,
        Context               $context,
        TaxCalculation        $taxCalculation
    ) {
        $this->directoryHelper = $directoryHelper;
        $this->storeManager    = $storeManager;
        $this->context         = $context;
        $this->taxCalculation  = $taxCalculation;
    }

    /**
     * Convert
     * | Converts price from base store currency to 'x' currency
     *
     * @param string $input
     * @param string $toCurrency
     *
     * @return float
     */
    public function convert($input, $toCurrency): float
    {
        $value = floatval($input);

        return $this->directoryHelper->currencyConvert(
            $value,
            $this->storeManager->getStore()->getBaseCurrencyCode(),
            $toCurrency
        );
    }

    /**
     * Include Tax
     * | Adds tax percent number to product price
     *
     * @param float $price
     *
     * @return float
     */
    public function inclTax($price): float
    {
        /** @var Product $product */
        $product = $this->context->getCurrentObject();

        $request = $this->taxCalculation->getRateRequest(null, null, null, $this->context->getFeed()->getStoreId());
        $request->setData('product_class_id', $product->getData('tax_class_id'));

        return $price + $this->taxCalculation->calcTaxAmount(
                $price,
                $this->taxCalculation->getRate($request),
                false,
                false
            );
    }

    /**
     * Exclude Tax
     * | Excludes tax percent number from the product price
     *
     * @param float $price
     *
     * @return float
     */
    public function exclTax($price): float
    {
        /** @var Product $product */
        $product = $this->context->getCurrentObject();

        $request = $this->taxCalculation->getRateRequest(null, null, null, $this->context->getFeed()->getStoreId());
        $request->setData('product_class_id', $product->getData('tax_class_id'));

        return $price - $this->taxCalculation->calcTaxAmount(
                $price,
                $this->taxCalculation->getRate($request),
                true,
                false
            );
    }
}
