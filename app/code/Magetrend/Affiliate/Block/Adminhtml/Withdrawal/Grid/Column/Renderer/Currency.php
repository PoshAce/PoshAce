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

namespace Magetrend\Affiliate\Block\Adminhtml\Withdrawal\Grid\Column\Renderer;

class Currency extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{

    public $moduleHelper;

    /**
     * Value constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        array $data = []
    ) {
        $this->moduleHelper = $moduleHelper;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $value = (int)$row->getData($this->getColumn()->getIndex());
        $value = $this->moduleHelper->formatPrice($value, $row->getCurrency());
        return $value;
    }
}
