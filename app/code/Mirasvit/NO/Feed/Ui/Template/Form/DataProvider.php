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

namespace Mirasvit\Feed\Ui\Template\Form;

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
use Magento\Ui\Component\Form\Fieldset;
use Magento\Ui\Component\Form\FieldsetFactory;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Model\Template;
use Mirasvit\Feed\Model\TemplateFactory;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class DataProvider extends \Magento\Framework\View\Element\UiComponent\DataProvider\DataProvider
{
    private $uiComponentFactory;

    private $context;

    private $fieldsetFactory;

    private $templateFactory;

    /**
     * @SuppressWarnings(PHPMD.ExcessiveParameterList)
     */
    public function __construct(
        ReportingInterface    $reporting,
        SearchCriteriaBuilder $searchCriteriaBuilder,
        RequestInterface      $request,
        FilterBuilder         $filterBuilder,
        UiComponentFactory    $uiComponentFactory,
        FieldsetFactory       $fieldsetFactory,
        ContextInterface      $context,
        TemplateFactory       $templateFactory,
        string                $name,
        string                $primaryFieldName,
        string                $requestFieldName,
        array                 $meta = [],
        array                 $data = []
    ) {
        $this->templateFactory    = $templateFactory;
        $this->uiComponentFactory = $uiComponentFactory;
        $this->context            = $context;
        $this->fieldsetFactory    = $fieldsetFactory;

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
            $itemData = $item->getData();
            $type     = $itemData['type'];

            if ($type == 'txt') {
                $type = 'csv';
            }

            $itemData['type_disabled'] = true;
            $tabsData['general']       = $itemData;
            $tabsData[$type]           = $itemData['format_serialized'] ? json_decode($itemData['format_serialized'], true) : '';

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

        $meta = $this->prepareForm($model);

        return $meta;
    }

    protected function prepareForm(Template $model): array
    {
        $type = $model->getType();

        if ($type == 'txt') {
            $type = 'csv';
        }

        $uiComponent = 'mst_feed_template_form_' . $type;

        $component = $this->uiComponentFactory->create($uiComponent);

        $data = $this->prepareComponent($component);

        return $data['children'];
    }


    protected function prepareComponent(UiComponentInterface $component): array
    {
        $data = [];
        foreach ($component->getChildComponents() as $name => $child) {
            $data['children'][$name] = $this->prepareComponent($child);
        }

        $data['arguments']['data']  = $component->getData();
        $data['arguments']['block'] = $component->getBlock();

        return $data;
    }

    private function getModel(): ?Template
    {
        $id = $this->context->getRequestParam($this->getRequestFieldName(), null);

        return $id ? $this->templateFactory->create()->load((int)$id) : null;
    }
}
