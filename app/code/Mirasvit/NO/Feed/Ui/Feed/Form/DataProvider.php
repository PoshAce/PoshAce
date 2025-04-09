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

namespace Mirasvit\Feed\Ui\Feed\Form;

use Magento\Backend\Model\Url;
use Magento\Framework\Api\FilterBuilder;
use Magento\Framework\Api\Search\ReportingInterface;
use Magento\Framework\Api\Search\SearchCriteriaBuilder;
use Magento\Framework\Api\Search\SearchResultInterface;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\Filesystem;
use Magento\Framework\View\Element\UiComponent\ContextInterface;
use Magento\Framework\View\Element\UiComponentFactory;
use Magento\Framework\View\Element\UiComponentInterface;
use Magento\Ui\Component\Form\FieldsetFactory;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\FeedFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $uiComponentFactory;

    private $context;

    private $feedFactory;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ReportingInterface    $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface      $request,
        FilterBuilder         $filterBuilder,
        UiComponentFactory    $uiComponentFactory,
        ContextInterface      $context,
        FeedFactory           $feedFactory,
                              $name,
                              $primaryFieldName,
                              $requestFieldName,
        array                 $meta = [],
        array                 $data = []
    ) {
        $this->feedFactory        = $feedFactory;
        $this->uiComponentFactory = $uiComponentFactory;
        $this->context            = $context;

        parent::__construct(
            $name,
            $primaryFieldName,
            $requestFieldName,
            $reporting,
            $searchCriteriaBuilder,
            $request,
            $filterBuilder,
            $meta,
            $data
        );
    }

    protected function searchResultToOutput(SearchResultInterface $searchResult): array
    {
        $arrItems          = [];
        $arrItems['items'] = [];
        foreach ($searchResult->getItems() as $item) {
            $itemData                 = $item->getData();
            $tabsData                 = $this->prepareData($itemData);
            $arrItems[$item->getId()] = $tabsData;
        }
        $arrItems['totalRecords'] = $searchResult->getTotalCount();

        return $arrItems;
    }

    public function getMeta(): array
    {
        $meta = parent::getMeta();


        $model = $this->getModel();
        if (!$model) {
            return $meta;
        }

        if (!$model->getType()) {
            return $meta;
        }

        $type = $model->getType();

        if ($type == 'txt') {
            $type = 'csv';
        }

        $uiComponent = 'mst_feed_feed_form_' . $type;
        $component   = $this->uiComponentFactory->create($uiComponent);

        $additionalComponent = $this->uiComponentFactory->create('mst_feed_feed_form_additional');
        foreach ($additionalComponent->getChildComponents() as $name => $child) {
            $component->addComponent($name, $child);
        }

        $data = $this->prepareComponent($component);

        return $data['children'];
    }

    protected function prepareComponent(UiComponentInterface $component): array
    {
        $data = [];
        foreach ($component->getChildComponents() as $name => $child) {
            $data['children'][$name] = $this->prepareComponent($child);
        }

        $component->prepare();
        $data['arguments']['data']  = $component->getData();
        $data['arguments']['block'] = $component->getBlock();

        if ($data['arguments']['data']['name'] == 'history_listing') {
            $data['arguments']['data']['config']['imports']['__disableTmpl'] = false;
            $data['arguments']['data']['config']['exports']['__disableTmpl'] = false;
        }

        return $data;
    }

    private function getModel(): ?Feed
    {
        $id = $this->context->getRequestParam($this->getRequestFieldName(), null);

        return $id ? $this->feedFactory->create()->load((int)$id) : null;
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity)
     */
    private function prepareData(array $itemData): array
    {
        $tabsData = [];

        $type    = $itemData['type'];
        $feed    = $this->feedFactory->create()->load($itemData['feed_id']);
        $ruleIds = $feed->getRuleIds();

        $tabsData['filter']['rule_ids'] = $ruleIds;
        if ($type == 'txt') {
            $type = 'csv';
        }

        foreach ($itemData as $key => $value) {
            switch ($key) {
                case substr($key, 0, 4) === "cron":
                    $tabsData['cron'][$key] = $value;
                    break;
                case substr($key, 0, 3) === "ftp":
                    $tabsData['ftp'][$key] = $value;
                    break;
                case substr($key, 0, 2) === "ga":
                    $tabsData['ga'][$key] = $value;
                    break;
                case substr($key, 0, 12) === "notification":
                    $tabsData['email'][$key] = $value;
                    break;
                case 'report_enabled':
                case 'filter_fastmode_enabled':
                case 'fb_metadata_enabled':
                    $tabsData['report'][$key] = $value;
                    break;
                case 'format_serialized':
                    $tabsData[$type] = $value ? SerializeService::decode($value) : '';
                    break;
                case 'rule_ids':
                    $tabsData['filter'][$key] = $value;
                    break;
                default:
                    $tabsData['general'][$key] = $value;
            }
            $tabsData['general']['type_disabled'] = true;
        }

        return $tabsData;
    }
}
