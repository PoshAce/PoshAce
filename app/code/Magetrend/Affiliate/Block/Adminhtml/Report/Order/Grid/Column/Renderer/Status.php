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

namespace Magetrend\Affiliate\Block\Adminhtml\Report\Order\Grid\Column\Renderer;

class Status extends \Magento\Backend\Block\Widget\Grid\Column\Renderer\Text
{
    /**
     * @var \Magetrend\GiftCard\Helper\Data
     */
    public $status;

    /**
     * Status constructor.
     * @param \Magento\Backend\Block\Context $context
     * @param \Magetrend\GiftCard\Helper\Data $helper
     * @param array $data
     */
    public function __construct(
        \Magento\Backend\Block\Context $context,
        \Magetrend\Affiliate\Model\Config\OrderStatus $status,
        array $data = []
    ) {
        $this->status = $status;
        parent::__construct($context, $data);
    }

    /**
     * @param \Magento\Framework\DataObject $row
     * @return string
     */
    public function render(\Magento\Framework\DataObject $row)
    {
        $options = $this->status->toArray();
        $status = $row->getData($this->getColumn()->getIndex());
        if (isset($options[$status])) {
            return $options[$status];
        }

        return $status;
    }
}
