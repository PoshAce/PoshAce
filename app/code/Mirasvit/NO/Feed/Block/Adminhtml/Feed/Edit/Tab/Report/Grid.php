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

namespace Mirasvit\Feed\Block\Adminhtml\Feed\Edit\Tab\Report;

use Magento\Backend\Block\Widget\Context;
use Magento\Backend\Block\Widget\Grid\Extended as ExtendedGrid;
use Magento\Backend\Helper\Data as BackendHelper;
use Magento\Framework\Registry;
use Mirasvit\Feed\Api\Data\ValidationInterface;
use Mirasvit\Feed\Repository\ValidationRepository;

class Grid extends ExtendedGrid
{

    protected $registry;

    private   $validationRepository;

    public function __construct(
        ValidationRepository $validationRepository,
        Registry             $registry,
        Context              $context,
        BackendHelper        $backendHelper
    ) {
        $this->validationRepository = $validationRepository;
        $this->registry             = $registry;

        parent::__construct($context, $backendHelper);
    }

    protected function _construct()
    {
        parent::_construct();

        $this->setId('report_grid');
        $this->setUseAjax(true);
    }

    protected function _prepareCollection(): ExtendedGrid
    {
        $model = $this->registry->registry('current_model');

        $collection = $this->validationRepository->getCollection()
            ->addFieldToFilter(ValidationInterface::FEED_ID, $model->getId());

        $this->setCollection($collection);

        return parent::_prepareCollection();
    }

    protected function _prepareColumns(): ExtendedGrid
    {
        $this->addColumn(ValidationInterface::VALIDATOR, [
            'header'         => __('Message'),
            'index'          => ValidationInterface::VALIDATOR,
            'frame_callback' => [$this, 'renderValidatorMessage'],
        ]);

        $this->addColumn(ValidationInterface::LINE_NUM, [
            'header' => __('Line #'),
            'index'  => ValidationInterface::LINE_NUM,
            'type'   => 'number',
        ]);

        $this->addColumn(ValidationInterface::ATTRIBUTE, [
            'header' => __('Attribute'),
            'index'  => ValidationInterface::ATTRIBUTE,
        ]);

        $this->addColumn(ValidationInterface::VALUE, [
            'header' => __('Value'),
            'index'  => ValidationInterface::VALUE,
        ]);

        return parent::_prepareColumns();
    }

    public function getRowUrl($item)
    {
        return false;
    }

    public function getGridUrl(): string
    {
        return $this->getUrl('*/*/reportGrid', ['_current' => true]);
    }

    /**
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function renderValidatorMessage(string $renderedValue, $item, $column, bool $isExport)
    {
        if ($this->validationRepository->getValidatorByCode($renderedValue)) {
            return $this->validationRepository->getValidatorByCode($renderedValue)->getMessage(true);
        }

        return $renderedValue;
    }
}
