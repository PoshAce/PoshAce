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

namespace Magetrend\Affiliate\Block\Adminhtml\Account\Grid\Column\Renderer;

/**
 * Fullname renderer block class
 */
class FullName extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrencyInterface;

    /**
     * Value constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        array $data = []
    ) {
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        if (empty($row->getData('customer_firstname'))
            || strpos($row->getData('customer_firstname'), 'Unknown') !== false) {
            return '-- --';
        }

        return $row->getData('customer_firstname').' '.$row->getData('customer_lastname');
    }
}
