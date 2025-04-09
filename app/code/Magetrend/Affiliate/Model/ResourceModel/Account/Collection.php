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

namespace Magetrend\Affiliate\Model\ResourceModel\Account;

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
            'Magetrend\Affiliate\Model\Account',
            'Magetrend\Affiliate\Model\ResourceModel\Account'
        );
    }

    public function joinCustomer($customerColumns = [])
    {
        $this->getSelect()->joinLeft(
            ['customer' => $this->getTable('customer_entity')],
            'main_table.customer_id = customer.entity_id',
            $customerColumns
        );

        return $this;
    }
}
