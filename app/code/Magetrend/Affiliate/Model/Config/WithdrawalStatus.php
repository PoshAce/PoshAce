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

use Magetrend\Affiliate\Model\Withdrawal;

class WithdrawalStatus implements \Magento\Framework\Option\ArrayInterface
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
            $optionArray = ['label' => __('General Information'), 'value' => $optionArray];
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
            ['value' => Withdrawal::STATUS_NEW, 'label' => __('New')],
            ['value' => Withdrawal::STATUS_PENDING, 'label' => __('Pending')],
            ['value' => Withdrawal::STATUS_PROCESSING, 'label' => __('Processing')],
            ['value' => Withdrawal::STATUS_COMPLETED, 'label' => __('Completed')],
            ['value' => Withdrawal::STATUS_CANCELED, 'label' => __('Canceled')],
        ];
    }
}
