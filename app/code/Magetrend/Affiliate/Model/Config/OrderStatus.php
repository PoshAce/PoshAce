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

namespace Magetrend\Affiliate\Model\Config;

use Magetrend\Affiliate\Model\Record\Order;

class OrderStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array of store contact variables
     *
     * @param bool $withGroup
     * @return array
     */
    public function toOptionArray($withGroup = false)
    {
        $optionArray = $this->getData();
        if ($withGroup && $optionArray) {
            $optionArray = ['label' => __('Order Status'), 'value' => $optionArray];
        }

        return $optionArray;
    }

    public function toArray()
    {
        $optionsArray = $this->getData();
        $data = [];
        foreach ($optionsArray as $option) {
            $data[$option['value']] = $option['label'];
        }

        return $data;
    }

    /**
     * Return available config variables
     * This method can be extended by plugin if you need to add more variables to the list
     *
     * @return array
     * @codeCoverageIgnore
     */
    public function getData()
    {
        return [
            ['value' => Order::ORDER_STATUS_NEW, 'label' => __('New')],
            ['value' => Order::ORDER_STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Order::ORDER_STATUS_PENDING_PAYMENT, 'label' => __('Pending Payment')],
            ['value' => Order::ORDER_STATUS_PROCESSING, 'label' => __('Processing')],
            ['value' => Order::ORDER_STATUS_COMPLETED, 'label' => __('Completed')],
            ['value' => Order::ORDER_STATUS_CANCELED, 'label' => __('Canceled')],
            ['value' => Order::ORDER_STATUS_CLOSED, 'label' => __('Closed')],
            ['value' => Order::ORDER_STATUS_HOLDED, 'label' => __('Holded')],
        ];
    }
}
