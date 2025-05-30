<?php
namespace Vdcstore\OrderGrid\Plugin;
use Magento\Framework\DB\Select;
use Magento\Framework\DB\Expr;

use Magento\Sales\Model\ResourceModel\Order\Grid\Collection;

class SalesOrderGridPlugin
{
    public function beforeLoad(
        \Magento\Sales\Model\ResourceModel\Order\Grid\Collection $collection,
        $printQuery = false,
        $logQuery = false
    ) {
        $select = $collection->getSelect();
    
        $select->joinLeft(
            ['soi' => $collection->getTable('sales_order_item')],
            'main_table.entity_id = soi.order_id AND soi.parent_item_id IS NULL',
            [
                'product_sku' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT soi.sku SEPARATOR ',<br>')"),
                'product_name' => new \Zend_Db_Expr("GROUP_CONCAT(DISTINCT soi.name SEPARATOR ',<br>')")
            ]
        );
    
        $select->joinLeft(
            ['btd' => $collection->getTable('braintree_transaction_details')],
            'main_table.entity_id = btd.order_id',
            ['transaction_source' => new \Zend_Db_Expr("MAX(btd.transaction_source)")]
        );
    
        $select->group('main_table.entity_id');
    }
}
