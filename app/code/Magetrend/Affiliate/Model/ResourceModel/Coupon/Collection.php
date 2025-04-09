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

namespace Magetrend\Affiliate\Model\ResourceModel\Coupon;

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
            'Magetrend\Affiliate\Model\Coupon',
            'Magetrend\Affiliate\Model\ResourceModel\Coupon'
        );
    }

    public function joinSalesRule($joinColumns = [])
    {
        $this->getSelect()->join(
            ['salesrule' => $this->getTable('salesrule')],
            'main_table.rule_id = salesrule.rule_id',
            $joinColumns
        );

        return $this;
    }

    public function joinProgram($joinColumns = [])
    {
        $this->getSelect()->join(
            ['program' => $this->getTable('mt_affiliate_program')],
            'main_table.program_id = program.entity_id',
            $joinColumns
        );

        return $this;
    }
}
