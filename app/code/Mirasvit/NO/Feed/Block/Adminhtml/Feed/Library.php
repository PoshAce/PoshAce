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

namespace Mirasvit\Feed\Block\Adminhtml\Feed;

use Magento\Backend\Block\Template;
use Magento\Backend\Block\Template\Context;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\Registry;
use Magento\Catalog\Model\Product;
use Magento\Catalog\Model\ResourceModel\Product\Collection;
use Mirasvit\Feed\Export\Resolver\ProductResolver;
use Mirasvit\Feed\Helper\Data as FeedHelper;
use Mirasvit\Feed\Export\Liquid\Context as LiquidContext;
use Mirasvit\Feed\Export\Liquid\Template as LiquidTemplate;

class Library extends Template
{
    protected $registry;

    protected $dataHelper;

    private   $productResolver;

    private   $productCollectionFactory;

    public function __construct(
        Registry                 $registry,
        Context                  $context,
        FeedHelper               $dataHelper,
        ProductCollectionFactory $productCollectionFactory,
        ProductResolver          $productResolver
    ) {
        $this->registry                 = $registry;
        $this->dataHelper               = $dataHelper;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->productResolver          = $productResolver;

        parent::__construct($context);
    }

    public function getRandomProducts(): Collection
    {
        $collection = $this->productCollectionFactory->create()
            ->addAttributeToSelect('name')
            ->setPageSize(5);
        $collection->getSelect()->orderRand();

        return $collection;
    }

    public function getPatternValue(Product $product): string
    {
        $liquidTemplate = new LiquidTemplate();
        $liquidTemplate->parse('{{ product.' . $this->getData('pattern') . ' }}');

        $liquidContext = new LiquidContext($this->productResolver, ['product' => $product]);

        //$liquidContext->addFilters($this->filterPool->getScopes());

        return $liquidTemplate->execute($liquidContext);
    }
}
