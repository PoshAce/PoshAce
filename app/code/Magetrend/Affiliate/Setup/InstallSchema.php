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

namespace Magetrend\Affiliate\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;
use Magento\Framework\Filesystem\Io\File;
use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\DB\Ddl\Table;

class InstallSchema implements InstallSchemaInterface
{

    private $affiliateProgram =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10, 'primary' => 1
        ],

        'name'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'description'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> null,
        ],

        'type'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'is_active'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_BOOLEAN,
            'length'=> '1',
        ],

        'currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'commission'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> null,
        ],

        'fixed_commission'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'coupon'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> null,
        ],

        'coupon_is_active'          => ['type' => Table::TYPE_SMALLINT,   'length'=> 1],
        'coupon_length'             => ['type' => Table::TYPE_INTEGER,    'length'=> 3],
        'coupon_format'             => ['type' => Table::TYPE_TEXT,       'length'=> 50],
        'coupon_prefix'             => ['type' => Table::TYPE_TEXT,       'length'=> 50],
        'coupon_suffix'             => ['type' => Table::TYPE_TEXT,       'length'=> 50],
        'coupon_dash'               => ['type' => Table::TYPE_SMALLINT,   'length'=> 2],

        'is_deleted'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> 1,
            'options' => ['nullable' => false, 'default' => 0],
        ],
    ];

    private $affiliateCoupon =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10, 'primary' => 1
        ],

        'coupon_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
        ],

        'rule_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
        ],

        'referral_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
        ],

        'program_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
        ],

        'coupon_code'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],
    ];

    private $affiliateAccount =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10, 'primary' => 1
        ],

        'website_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> '6',
        ],

        'store_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> '6',
        ],

        'customer_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'status'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'email'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'base_currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'base_balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],
        
        'base_reserved_balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'reserved_balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'base_available_balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'available_balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'base_payout_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'payout_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
            'options' => ['nullable' => false],
        ],

        'referral_code'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'paypal_account_email'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'program'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> null,
        ],

        'ip'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '15',
        ],

        'require_recalculate'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 1,
            'options' => ['nullable' => false],
        ],

        'created_at'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],
    ];

    private $affiliateWithdrawal =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10, 'primary' => 1
        ],

        'increment_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '20',
        ],

        'referral_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'created_at'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],

        'finished_at'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],

        'base_amount_request'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'amount_request'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'base_amount_paid'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'amount_paid'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'status'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'commnet'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'base_currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ]

    ];

    private $affiliateClick =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10, 'primary' => 1
        ],

        'referral_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'date'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],

        'ip'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '15',
        ],

        'url'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'status'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ]
    ];

    private $affiliateRecordBalance =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10, 'primary' => 1
        ],

        'code'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '850',
        ],

        'related_entity_type'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'related_entity_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'referral_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'base_currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'base_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'base_balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'balance'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'comment'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> null,
        ],

        'created_at'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],

    ];

    private $affiliateTransaction =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'referral_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'object_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'object_type'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'order_counter'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 3,
            'options' => ['unsigned' => true]
        ],

        'order_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'order_increment_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'order_status'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'customer_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'customer_name'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'program_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'base_currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'base_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'base_commission'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'commission'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'comment'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'created_at'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],

        'status'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],
    ];

    private $affiliateRecordOrder =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'order_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'order_increment_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'order_status'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'order_counter'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 3,
            'options' => ['unsigned' => true]
        ],

        'referral_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'customer_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'customer_name'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'base_currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'currency'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '6',
        ],

        'base_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'base_invoiced_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'invoiced_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'base_refunded_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'refunded_amount'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'base_commissions'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'commissions'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DECIMAL,
            'length'=> '12,4',
        ],

        'comment'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'status'                => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '50',
        ],

        'created_at'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_DATETIME,
            'length'=> null,
        ],

        'is_visible'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> '1',
            'options' => ['unsigned' => true, 'nullable' => false, 'default' => 0]
        ],
    ];

    private $affiliateSalesOrder =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'order_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'referral_code'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],

        'required_update'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> '1',
        ],
    ];

    private $affiliateSalesInvoice =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'invoice_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'order_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'required_update'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> '1',
        ],
    ];

    private $affiliateSalesCreditmemo =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'creditmemo_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'order_id'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'options' => ['unsigned' => true]
        ],

        'required_update'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_SMALLINT,
            'length'=> '1',
        ],
    ];

    private $formBuilderForm =  [
        'entity_id'                 => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
            'length'=> 10,
            'primary' => 1
        ],

        'name'               => [
            'type' => \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
            'length'=> '255',
        ],
    ];

    private $formBuilderField =  [
        'entity_id'      => ['type' => Table::TYPE_INTEGER,    'length'=> 10, 'primary' => 1],
        'form_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
        ],
        'type'                      => ['type' => Table::TYPE_TEXT,       'length'=> 20],
        'name'                      => ['type' => Table::TYPE_TEXT,       'length'=> 255],
        'label'                     => ['type' => Table::TYPE_TEXT,       'length'=> 255],
        'frontend_label'            => ['type' => Table::TYPE_TEXT,       'length'=> 255],
        'default_value'             => ['type' => Table::TYPE_TEXT,       'length'=> null],
        'error_message'             => ['type' => Table::TYPE_TEXT,       'length'=> null],
        'is_required'               => ['type' => Table::TYPE_SMALLINT,   'length'=> 1],
        'position'                  => ['type' => Table::TYPE_SMALLINT,   'length'=> 6],
        'after_email_field'         => ['type' => Table::TYPE_SMALLINT,   'length'=> 1],
    ];

    private $formBuilderOption =  [
        'entity_id'    => ['type' => Table::TYPE_INTEGER,    'length'=> 10, 'primary' => 1],
        'field_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
        ],
        'value'                     => ['type' => Table::TYPE_TEXT,       'length'=> 20],
        'label'                     => ['type' => Table::TYPE_TEXT,       'length'=> 255],
        'position'                  => ['type' => Table::TYPE_SMALLINT,   'length'=> 6],
    ];

    private $formBuilderData =  [
        'entity_id'    => ['type' => Table::TYPE_INTEGER,    'length'=> 10, 'primary' => 1],
        'object_id' => [
            'type' => Table::TYPE_INTEGER,
            'length'=> 10,
        ],
        'object_type' => ['type' => Table::TYPE_TEXT,       'length'=> 50],
        'field_key'   => ['type' => Table::TYPE_TEXT,       'length'=> 255],
        'field_value' => ['type' => Table::TYPE_TEXT,       'length'=> null],
    ];

    public function install(SchemaSetupInterface $setup, ModuleContextInterface $context)
    {
        $installer = $setup;
        $installer->startSetup();
        $db = $installer->getConnection();

        $this->createTable($installer, $db->getTableName('mt_affiliate_program'), $this->affiliateProgram);
        $this->createTable($installer, $db->getTableName('mt_affiliate_coupon'), $this->affiliateCoupon);
        $this->createTable($installer, $db->getTableName('mt_affiliate_account'), $this->affiliateAccount);
        $this->createTable($installer, $db->getTableName('mt_affiliate_record_click'), $this->affiliateClick);
        $this->createTable($installer, $db->getTableName('mt_affiliate_record_balance'), $this->affiliateRecordBalance);
        $this->createTable(
            $installer,
            $db->getTableName('mt_affiliate_record_transaction'),
            $this->affiliateTransaction
        );
        $this->createTable($installer, $db->getTableName('mt_affiliate_record_order'), $this->affiliateRecordOrder);
        $this->createTable($installer, $db->getTableName('mt_affiliate_withdrawal'), $this->affiliateWithdrawal);
        $this->createTable($installer, $db->getTableName('mt_affiliate_sales_order'), $this->affiliateSalesOrder);
        $this->createTable($installer, $db->getTableName('mt_affiliate_sales_invoice'), $this->affiliateSalesInvoice);
        $this->createTable(
            $installer,
            $db->getTableName('mt_affiliate_sales_creditmemo'),
            $this->affiliateSalesCreditmemo
        );
        $this->createTable($installer, $db->getTableName('mt_affiliate_formbuilder_form'), $this->formBuilderForm);
        $this->createTable($installer, $db->getTableName('mt_affiliate_formbuilder_field'), $this->formBuilderField);
        $this->createTable($installer, $db->getTableName('mt_affiliate_formbuilder_option'), $this->formBuilderOption);
        $this->createTable($installer, $db->getTableName('mt_affiliate_formbuilder_data'), $this->formBuilderData);

        $installer->endSetup();
    }

    public function createTable($installer, $tableName, $columns)
    {
        $db = $installer->getConnection();
        $table = $db->newTable($tableName);
        foreach ($columns as $name => $info) {
            $uniqueIndex = false;
            if (isset($info['unique'])) {
                unset($info['unique']);
                $uniqueIndex = true;
            }

            if (isset($info['primary']) && $info['primary'] == 1) {
                $options = ['identity' => true, 'nullable' => false, 'primary' => true];
            } elseif (isset($info['options'])) {
                $options = $info['options'];
            } else {
                $options = [];
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

            if ($uniqueIndex) {
                $table->addIndex(
                    $installer->getIdxName(
                        $installer->getIdxName($tableName, [$name]),
                        [$name],
                        \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE
                    ),
                    [$name],
                    ['type' => \Magento\Framework\DB\Adapter\AdapterInterface::INDEX_TYPE_UNIQUE]
                );
            }
        }

        $db->createTable($table);
    }
}
