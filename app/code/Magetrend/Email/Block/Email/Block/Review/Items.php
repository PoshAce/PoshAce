<?php
/**
 * Copyright © 2016 MB Vienas bitas. All rights reserved.
 * @website    www.magetrend.com
 * @package    MT Email for M2
 * @author     Edvinas Stulpinas <edwin@magetrend.com>
 */

namespace Magetrend\Email\Block\Email\Block\Review;

class Items extends \Magetrend\Email\Block\Email\Block\Template
{
    private $collection = null;

    public $productRepository;

    public $searchCriteriaBuilder;

    public $productStatus;

    public $registry;

    public $stockState;

    public function __construct(
        \Magento\Framework\View\Element\Template\Context $context,
        \Magento\Catalog\Api\ProductRepositoryInterface $productRepository,
        \Magento\Framework\Api\SearchCriteriaBuilder $searchCriteriaBuilder,
        \Magento\Catalog\Model\Product\Attribute\Source\Status $productStatus,
        \Magento\Framework\Registry $registry,
        \Magento\CatalogInventory\Api\StockStateInterface $stockState,
        array $data = []
    ) {
        $this->productRepository = $productRepository;
        $this->searchCriteriaBuilder = $searchCriteriaBuilder;
        $this->productStatus = $productStatus;
        $this->registry = $registry;
        $this->stockState = $stockState;
        parent::__construct($context, $data);
    }

    public function getItems()
    {
        return $this->getOrder()->getAllVisibleItems();
    }

    public function getRelatedProducts()
    {
        $order = $this->getOrder();
        $items = $order->getAllItems();

        if (empty($items)) {
            $this->collection = [];
        }

        $itemIds = [];
        foreach ($items as $item) {
            $itemIds[$item->getProductId()] = 1;
        }

        $searchCriteria = $this->searchCriteriaBuilder
            ->addFilter('entity_id', array_keys($itemIds), 'in')
            ->create();
        $products = $this->productRepository->getList($searchCriteria);

        $relatedProducts = [];
        if ($products->getTotalCount() > 0) {
            $productCollection = $products->getItems();
            foreach ($productCollection as $product) {
                $rProducts = $product->getRelatedProducts();

                foreach ($rProducts as $rProduct) {
                    if (!$this->stockState->getStockQty($rProduct->getId())) {
                        continue;
                    }
                    $relatedProducts[] = $rProduct;
                }
            }
        }

        return $relatedProducts;
    }

    public function getProductHtml($product, $itemNumber = 0)
    {
        if (!$product) {
            return '';
        }

        $childNames = $this->getChildNames();
        $rendererName = 'block.review_products.'.$product->getTypeId();
        if (in_array($rendererName, $childNames)) {
            $renderer = $this->getChildBlock($rendererName);
        } else {
            $renderer = $this->getChildBlock('block.review_products.default');
        }

        return $renderer
            ->setItemNumber($itemNumber)
            ->setItem($product)
            ->setVarModel($this->getVarModel())
            ->toHtml();
    }
}
