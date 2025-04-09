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

namespace Mirasvit\Feed\Ui\Feed\Form\Control;

use Magento\Backend\Block\Widget\Context;
use Magento\Framework\Registry;
use Mirasvit\Feed\Helper\Data as FeedHelper;
use Mirasvit\Feed\Model\Feed;

class DeliveryButton extends AbstractButton
{
    protected $registry;

    protected $dataHelper;

    public function __construct
    (
        Registry   $registry,
        FeedHelper $dataHelper,
        Context    $context
    ) {
        $this->registry   = $registry;
        $this->dataHelper = $dataHelper;
        parent::__construct($context);
    }

    public function getButtonData(): array
    {
        if ($this->getModel()->getFtp()) {
            $deliveryUrl = $this->dataHelper->getFeedDeliverUrl($this->getModel());

            return [
                'label'      => (string)__('Delivery Feed'),
                'class'      => 'delivery',
                'on_click'   => 'setLocation(\'' . $deliveryUrl . '\')',
                'sort_order' => -90,
            ];
        }

        return [];
    }

    public function getModel(): ?Feed
    {
        return $this->registry->registry('current_model');
    }
}
