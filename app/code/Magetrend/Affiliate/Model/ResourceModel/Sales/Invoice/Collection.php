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

namespace Magetrend\Affiliate\Model\ResourceModel\Sales\Invoice;

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
            'Magetrend\Affiliate\Model\Sales\Invoice',
            'Magetrend\Affiliate\Model\ResourceModel\Sales\Invoice'
        );
    }

    public function joinTransactoionTable($columns = [])
    {
        $this->getSelect()->joinLeft(
            ['transaction' => $this->getTable('mt_affiliate_record_transaction')],
            "main_table.invoice_id = transaction.object_id AND transaction.object_type = 'invoice'",
            $columns
        );
        return $this;
    }

    public function joinOrderTable($columns = [])
    {
        $this->getSelect()->joinLeft(
            ['order' => $this->getTable('mt_affiliate_sales_order')],
            "main_table.order_id = order.order_id",
            $columns
        );
        return $this;
    }
}
