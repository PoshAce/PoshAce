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

namespace Mirasvit\Feed\Ui\Feed\Form\History;

use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Registry;
use Mirasvit\Feed\Model\ResourceModel\Feed\History\CollectionFactory;
use Magento\Framework\Api\Search\SearchCriteria;
use Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider as UiDataProvider;

class DataProvider extends UiDataProvider
{
    protected $registry;

    /**
     * {@inheritdoc}
     * @SuppressWarnings(PHPMD)
     */
    public function __construct(
        Registry              $registry,
        string                $name,
        string                $primaryFieldName,
        string                $requestFieldName,
        ReportingInterface    $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface      $request,
        FilterBuilder         $filterBuilder,
        array                 $meta = [],
        array                 $data = []
    ) {
        $this->registry = $registry;

        parent::__construct($name, $primaryFieldName, $requestFieldName, $reporting, $searchCriteriaBuilder, $request, $filterBuilder, $meta, $data);
    }

    public function getSearchCriteria(): SearchCriteria
    {
        if ($this->request->getParam('feed_id')) {
            $feedId = intval($this->request->getParam('feed_id'));

            $filter = $this->filterBuilder
                ->setField('feed_id')
                ->setValue($feedId)->create();

            $this->searchCriteriaBuilder->addFilter($filter);
        }

        return parent::getSearchCriteria();
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $arrItems          = [];
        $arrItems['items'] = [];

        foreach ($searchResult->getItems() as $item) {
            $itemData                 = $item->getData();
            $itemData['created_at']   = date("M j, Y, g:i A", strtotime($itemData['created_at']));
            $itemData['message']      = nl2br(htmlentities($itemData['message']));
            $arrItems['items'][]      = $itemData;
            $arrItems['totalRecords'] = $searchResult->getTotalCount();

        }

        return $arrItems;
    }
}
