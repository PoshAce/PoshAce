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

namespace Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Option;

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
            'Magetrend\Affiliate\Model\FormBuilder\Option',
            'Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Option'
        );
    }

    public function setFieldFilter($fieldId)
    {
        $this->addFieldToFilter('field_id', $fieldId);
        return $this;
    }
}
