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

use Magento\Catalog\Model\Category\StoreCategories;
use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory as ProductCollectionFactory;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\ResourceConnection;
use Magento\Framework\ObjectManagerInterface;
use Magento\Review\Model\ResourceModel\Review\CollectionFactory as ReviewCollectionFactory;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Mirasvit\Feed\Export\Context;
use Magento\Store\Model\Store;
use Magento\Catalog\Model\ResourceModel\Product\Collection as ProductCollection;
use Magento\Catalog\Model\ResourceModel\Category\Collection as CategoryCollection;
use Magento\Review\Model\ResourceModel\Review\Collection as ReviewCollection;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class GeneralResolver extends AbstractResolver
{
    protected $productCollectionFactory;

    protected $categoryCollectionFactory;

    protected $reviewCollectionFactory;

    protected $timezone;

    protected $pool;

    protected $resource;

    protected $storeCategories;

    public function __construct(
        ResourceConnection        $resource,
        ProductCollectionFactory  $productCollectionFactory,
        CategoryCollectionFactory $categoryCollectionFactory,
        ReviewCollectionFactory   $reviewCollectionFactory,
        TimezoneInterface         $timezone,
        Pool                      $pool,
        Context                   $context,
        ObjectManagerInterface    $objectManager,
        StoreCategories           $storeCategories
    ) {
        $this->productCollectionFactory  = $productCollectionFactory;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->reviewCollectionFactory   = $reviewCollectionFactory;
        $this->timezone                  = $timezone;
        $this->pool                      = $pool;
        $this->resource                  = $resource;
        $this->storeCategories           = $storeCategories;

        parent::__construct($context, $objectManager);
    }

    public function getAttributes(): array
    {
        return [];
    }

    public function getStore(): Store
    {
        return $this->context->getFeed()->getStore();
    }


    public function getTime()
    {
        return $this->timezone->date()->format("d.m.Y H:i:s");
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getProducts($object = null, array $args = []): ProductCollection
    {
        $collection = $this->productCollectionFactory->create()
            ->addStoreFilter();

        $collection->setFlag('NO_SORT', true);

        if ($this->context->isTestMode()) {
            $collection->getSelect()->limit(10);

            /** @var RequestInterface $request */
            $request = $this->objectManager->get(RequestInterface::class);

            if ($request->getParam('preview_ids')) {
                $ids = explode(',', $request->getParam('preview_ids'));
            } else {
                $random = $this->resource->getConnection()->fetchAll(
                    $collection->getSelect()
                        ->limit(10)
                        ->order('rand()')
                );

                $ids = array_map(function ($item) {
                    return $item['entity_id'];
                }, $random);
            }

            $ids[] = 0;

            $collection->getSelect()
                ->reset('limitcount')
                ->reset('limitoffset')
                ->reset('order');

            $collection->addFieldToFilter('entity_id', $ids);
        } else {
            if (count($this->context->getFeed()->getRuleIds())) {
                $collection->getSelect()->joinLeft(
                    ['rule' => $this->resource->getTableName('mst_feed_feed_product')],
                    'e.entity_id=rule.product_id',
                    []
                )->where('rule.feed_id = ?', $this->context->getFeed()->getId())
                    ->where('rule.is_new = 1');
            }

            $collection->setFlag('has_stock_status_filter', true);
            if (isset($args['index'])) {
                $collection->getSelect()->limit($args['length'], $args['index']);
                $collection->setStore($this->context->getFeed()->getStore())
                    ->load();
            }
        }

        return $collection;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getCategories($object = null, array $args = []): CategoryCollection
    {
        $childIds = $this->storeCategories->getCategoryIds((int)$this->getFeed()->getStoreId());

        $collection = $this->categoryCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $childIds])
            ->addIsActiveFilter();

        if ($this->context->isTestMode()) {
            $collection->getSelect()->limit(10);
        }

        return $collection;
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function getReviews($object = null, array $args = []): ReviewCollection
    {
        $collection = $this->reviewCollectionFactory->create()
            ->addStoreFilter($this->context->getFeed()->getStore()->getId())
            ->addStatusFilter(1);

        if ($this->context->isTestMode()) {
            $collection->getSelect()->limit(10);

            $random = $this->resource->getConnection()->fetchAll(
                $collection->getSelect()
                    ->limit(10)
                    ->order('rand()')
            );
            $ids    = array_map(function ($item) {
                return $item['review_id'];
            }, $random);

            $collection->getSelect()
                ->reset('limitcount')
                ->reset('limitoffset')
                ->reset('order');

            $collection->addFieldToFilter('main_table.review_id', $ids);
        } else {
            // Apply product filter
            $productIds = $this->getProducts()->getAllIds();
            $collection->addFieldToFilter('entity_pk_value', ['in' => $productIds]);
        }

        $collection->addRateVotes();

        return $collection;
    }
}
