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

namespace Magetrend\Affiliate\Model\ResourceModel\Withdrawal;

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
            'Magetrend\Affiliate\Model\Withdrawal',
            'Magetrend\Affiliate\Model\ResourceModel\Withdrawal'
        );
    }

    public function joinAffiliate($joinColumns = [])
    {
        $this->getSelect()->join(
            ['affiliate' => $this->getTable('mt_affiliate_account')],
            'main_table.referral_id = affiliate.entity_id',
            $joinColumns
        );

        return $this;
    }

    public function joinCustomer($customerColumns = [], $affiliateColumns = [])
    {
        $this->joinAffiliate(array_merge($affiliateColumns, ['customer_id']));
        $this->getSelect()->join(
            ['customer' => $this->getTable('customer_entity')],
            'affiliate.customer_id = customer.entity_id',
            $customerColumns
        );

        return $this;
    }
}
