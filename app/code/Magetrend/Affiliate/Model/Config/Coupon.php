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

class Coupon implements \Magento\Framework\Option\ArrayInterface
{
    public $collectionFactory;

    public function __construct(
        \Magento\SalesRule\Model\ResourceModel\Rule\CollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    /**
     * Options getter
     *
     * @return array
     */
    public function toOptionArray()
    {
        $options = $this->toArray();
        $optionsArray = [];
        foreach ($options as $value => $label) {
            $optionsArray[] = ['value' => $value,  'label' => $label];
        }

        return $optionsArray;
    }

    /**
     * Get options in "key-value" format
     *
     * @return array
     */
    public function toArray()
    {
        $dataArray = [];
        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('use_auto_generation', 1);
        if ($collection->getSize() > 0) {
            foreach ($collection as $rule) {
                $dataArray[$rule->getId()] = $rule->getName();
            }
        }

        return $dataArray;
    }
}
