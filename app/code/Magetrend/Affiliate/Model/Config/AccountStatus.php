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

use Magetrend\Affiliate\Model\Account;

class AccountStatus implements \Magento\Framework\Option\ArrayInterface
{
    /**
     * Retrieve option array of store contact variables
     *
     * @param bool $withGroup
     * @return array
     */
    public function toOptionArray($excludeSystemStatus = false)
    {
        $optionArray = $this->getData();
        if ($excludeSystemStatus) {
            foreach ($optionArray as $key => $option) {
                if (in_array($option['value'], [
                    Account::STATUS_NEW,
                    Account::STATUS_DELETED,
                    Account::STATUS_CANCELED
                ])) {
                    unset($optionArray[$key]);
                }
            }
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
            ['value' => Account::STATUS_NEW, 'label' => __('New')],
            ['value' => Account::STATUS_ACTIVE, 'label' => __('Active')],
            ['value' => Account::STATUS_INACTIVE, 'label' => __('Inactice')],
            ['value' => Account::STATUS_CANCELED, 'label' => __('Canceled')],
            ['value' => Account::STATUS_DELETED, 'label' => __('Deleted')],
        ];
    }
}
