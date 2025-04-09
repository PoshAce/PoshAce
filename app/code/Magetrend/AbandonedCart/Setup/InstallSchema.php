<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * PHP version 5.3 or later
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */

/**
 * @codingStandardsIgnoreFile
 */
namespace Magetrend\AbandonedCart\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use \Magento\Framework\DB\Ddl\Table;

/**
 * Installation script class
 *
 * @category MageTrend
 * @package  Magetend/AbandonedCart
 * @author   Edvinas Stulpinas <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     https://www.magetrend.com/magento-2-abandoned-cart
 */
class InstallSchema implements InstallSchemaInterface
{
    /**
     * Table mt_ac_email columns source
     *
     * @var array
     */
    private $customerColumns =  [
        'entity_id'                 => ['type' => Table::TYPE_INTEGER,      'length'=> 10, 'primary' => 1],
        'email'                     => ['type' => Table::TYPE_TEXT,         'length'=> 255],
        'hash'                      => ['type' => Table::TYPE_TEXT,         'length'=> 32],
        'ip'                        => ['type' => Table::TYPE_TEXT,         'length'=> 15],
        'trust'                     => ['type' => Table::TYPE_INTEGER,      'length'=> 3],
        'created_at'                => ['type' => Table::TYPE_DATETIME,     'length'=> null],
        'updated_at'                => ['type' => Table::TYPE_DATETIME,     'length'=> null],
    ];


    /**
     * Table mt_ac_rule columns source
     *
     * @var array
     */
    private $ruleColumns =  [
        'entity_id'                 => ['type' => Table::TYPE_INTEGER,      'length'=> 10, 'primary' => 1],
        'is_active'                 => ['type' => Table::TYPE_SMALLINT,     'length'=> 1],
        'name'                      => ['type' => Table::TYPE_TEXT,         'length'=> 255],
        'type'                      => ['type' => Table::TYPE_TEXT,         'length'=> 255],
        'store_ids'                 => ['type' => Table::TYPE_TEXT,         'length'=> null],
        'customer_groups'           => ['type' => Table::TYPE_TEXT,         'length'=> null],
        'trigger_events'            => ['type' => Table::TYPE_TEXT,         'length'=> null],
        'cancel_events'             => ['type' => Table::TYPE_TEXT,         'length'=> null],
        'payment_methods'           => ['type' => Table::TYPE_TEXT,         'length'=> null],
        'coupon_expire_in_days'     => ['type' => Table::TYPE_TEXT,         'length'=> 10],
        'coupon_length'             => ['type' => Table::TYPE_INTEGER,      'length'=> 3],
        'coupon_format'             => ['type' => Table::TYPE_TEXT,         'length'=> 50],
        'coupon_prefix'             => ['type' => Table::TYPE_TEXT,         'length'=> 50],
        'coupon_suffix'             => ['type' => Table::TYPE_TEXT,         'length'=> 50],
        'coupon_dash'               => ['type' => Table::TYPE_SMALLINT,     'length'=> 2],
        'priority'                  => ['type' => Table::TYPE_SMALLINT,     'length'=> 5],
        'conditions_serialized'     => ['type' => Table::TYPE_TEXT,         'length'=> '2M'],
        'color_1'                   => ['type' => Table::TYPE_TEXT,         'length'=> 10],
        'color_2'                   => ['type' => Table::TYPE_TEXT,         'length'=> 10],
        'font_size_1'               => ['type' => Table::TYPE_TEXT,         'length'=> 10],
        'font_1'                    => ['type' => Table::TYPE_TEXT,         'length'=> 10],
        'bar_text'                  => ['type' => Table::TYPE_TEXT,         'length'=> null],
        'item_qty'                  => ['type' => Table::TYPE_SMALLINT,     'length'=> 5],
        'show_after'                => ['type' => Table::TYPE_SMALLINT,     'length'=> 5],
        'hide_after'                => ['type' => Table::TYPE_SMALLINT,     'length'=> 5],
        'delay_time'                => ['type' => Table::TYPE_FLOAT,        'length'=> null],
    ];

    /**
     * Table mt_ac_schedule columns source
     *
     * @var array
     */
    private $scheduleColumns =  [
        'entity_id'                 => ['type' => Table::TYPE_INTEGER,      'length'=> 10, 'primary' => 1],
        'rule_id'                   => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'sales_rule_id'             => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'sort_order'                => ['type' => Table::TYPE_SMALLINT,     'length'=> 5],
        'email_template'            => ['type' => Table::TYPE_TEXT,         'length'=> 255],
        'delay_day'                 => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'delay_hour'                => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'delay_minute'              => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
    ];

    /**
     * Table mt_ac_email_queue columns source
     *
     * @var array
     */
    private $emailQueueColumns =  [
        'entity_id'                 => ['type' => Table::TYPE_INTEGER,      'length'=> 10, 'primary' => 1],
        'store_id'                  => ['type' => Table::TYPE_INTEGER,      'length'=> 6],
        'group_hash'                => ['type' => Table::TYPE_TEXT,         'length'=> 32],
        'quote_id'                  => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'order_id'                  => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'schedule_id'               => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'rule_id'                   => ['type' => Table::TYPE_INTEGER,      'length'=> 10],
        'customer_email'            => ['type' => Table::TYPE_TEXT,         'length'=> 255],
        'created_at'                => ['type' => Table::TYPE_DATETIME,     'length'=> null],
        'scheduled_at'              => ['type' => Table::TYPE_DATETIME,     'length'=> null],
        'sent_at'                   => ['type' => Table::TYPE_DATETIME,     'length'=> null],
        'status'                    => ['type' => Table::TYPE_TEXT,         'length'=> 50],
    ];


    /**
     * Execute installation script
     *
     * @param SchemaSetupInterface $setup
     * @param ModuleContextInterface $context
     */
    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;

        $installer->startSetup();

        $this->createTable(
            $installer,
            $installer->getTable('mt_ac_visitor'),
            $this->customerColumns
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_ac_rule'),
            $this->ruleColumns
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_ac_schedule'),
            $this->scheduleColumns
        );

        $this->createTable(
            $installer,
            $installer->getTable('mt_ac_email_queue'),
            $this->emailQueueColumns
        );


        $db = $installer->getConnection();
        $db->addColumn(
            $installer->getTable('quote'),
            'visitor_hash',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '32',
                'comment'   => 'Visitor Hash',
                'nullable'   => true,
            ]
        );

        $db->addColumn(
            $installer->getTable('quote'),
            'ac_status',
            [
                'type'      => Table::TYPE_INTEGER,
                'length'    => 2,
                'comment'   => 'Abandoned Cart Status',
                'nullable'   => false,
            ]
        );

        $db->addColumn(
            $installer->getTable('quote'),
            'ac_hash',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '32',
                'comment'   => 'Cart Restore Hash',
                'nullable'   => true,
            ]
        );

        $db->addColumn(
            $installer->getTable('sales_order'),
            'ac_status',
            [
                'type'      => Table::TYPE_INTEGER,
                'length'    => 2,
                'comment'   => 'Follow up Status',
                'nullable'   => false,
            ]
        );

        $db->addColumn(
            $installer->getTable('sales_order'),
            'ac_hash',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '32',
                'comment'   => 'Cart Restore Hash',
                'nullable'   => true,
            ]
        );

        $db->addColumn(
            $installer->getTable('salesrule_coupon'),
            'ac_group_hash',
            [
                'type'      => Table::TYPE_TEXT,
                'length'    => '32',
                'comment'   => 'Abandoned cart email chain hash',
                'nullable'   => true,
            ]
        );

        $installer->endSetup();
    }

    /**
     * Create database table
     *
     * @param $installer
     * @param $tableName
     * @param $columns
     */
    public function createTable($installer, $tableName, $columns)
    {
        $db = $installer->getConnection();
        $table = $db->newTable($tableName);

        foreach ($columns as $name => $info) {
            $options = [];
            if (isset($info['options'])) {
                $options = $info['options'];
            }

            if (isset($info['primary']) && $info['primary'] == 1) {
                $options = ['identity' => true, 'nullable' => false, 'primary' => true];
            }

            $table->addColumn(
                $name,
                $info['type'],
                $info['length'],
                $options,
                $name
            );

            if (isset($info['index'])) {
                $table->addIndex(
                    $installer->getIdxName($tableName, [$name]),
                    [$name]
                );
            }

            if (isset($info['foreign_key'])) {
                $table->addForeignKey(
                    $installer->getFkName($tableName, $name, $info['foreign_key'][0], $info['foreign_key'][1]),
                    $name,
                    $installer->getTable($info['foreign_key'][0]),
                    $info['foreign_key'][1],
                    Table::ACTION_NO_ACTION
                );
            }
        }

        $db->createTable($table);
    }
}

