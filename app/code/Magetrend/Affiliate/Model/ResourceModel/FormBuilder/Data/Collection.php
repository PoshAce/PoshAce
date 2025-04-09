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

namespace Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Data;

class Collection extends \Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection
{

    /**
     * Define resource model
     *
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init(
            'Magetrend\Affiliate\Model\FormBuilder\Data',
            'Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Data'
        );
    }

    public function addObjectFilter($objectId, $objectType)
    {
        $this->addFieldToFilter('object_id', $objectId)
            ->addFieldToFilter('object_type', $objectType);
        return $this;
    }
}
