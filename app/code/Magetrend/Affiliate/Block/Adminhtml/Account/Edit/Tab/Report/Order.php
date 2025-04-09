<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Block\Adminhtml\Account\Edit\Tab\Report;

use \Magento\Backend\Block\Widget\Form\Generic;
use \Magento\Backend\Block\Widget\Tab\TabInterface;

/**
 * Account orders tab block class
 */
class Order extends \Magento\Backend\Block\Text\ListText implements \Magento\Backend\Block\Widget\Tab\TabInterface
{
    /**
     * Core registry
     *
     * @var \Magento\Framework\Registry
     */
    public $coreRegistry = null;

    /**
     * @param \Magento\Framework\View\Element\Context $context
     * @param \Magento\Framework\Registry $registry
     * @param array $data
     */
    public function __construct(
        \Magento\Framework\View\Element\Context $context,
        \Magento\Framework\Registry $registry,
        array $data = []
    ) {
        $this->coreRegistry = $registry;
        parent::__construct($context, $data);
    }

    /**
     * {@inheritdoc}
     */
    public function getTabLabel()
    {
        return __('Order Report');
    }

    /**
     * {@inheritdoc}
     */
    public function getTabTitle()
    {
        return __('Order Report');
    }

    /**
     * {@inheritdoc}
     */
    public function canShowTab()
    {
        return $this->_isEditing();
    }

    /**
     * {@inheritdoc}
     */
    public function isHidden()
    {
        return !$this->_isEditing();
    }

    /**
     * Check whether we edit existing rule or adding new one
     *
     * @return bool
     */
    public function _isEditing()
    {
        $priceRule = $this->coreRegistry->registry('current_promo_quote_rule');
        return $priceRule->getRuleId() !== null;
    }

    /**
     * Before rendering html, but after trying to load cache
     *
     * @return $this
     */
    public function _beforeToHtml()
    {
        $this->setChild(
            'generate_code_grid',
            $this->getLayout()->createBlock('Magetrend\Affiliate\Block\Adminhtml\Report\Order\Grid')
        );
        return parent::_beforeToHtml();
    }
}
