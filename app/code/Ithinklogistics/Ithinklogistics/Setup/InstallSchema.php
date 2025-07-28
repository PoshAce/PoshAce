<?php

/**
 * Ithinklogistics
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade this extension to newer
 * version in the future.
 *
 * @category    Ithinklogistics
 * @package     Ithinklogistics_Ithinklogistics
 * @copyright   Copyright (c) Ithinklogistics (https://www.ithinklogistics.com/)
 */ 

namespace Ithinklogistics\Ithinklogistics\Setup;

use Magento\Framework\Setup\InstallSchemaInterface;
use Magento\Framework\Setup\ModuleContextInterface;
use Magento\Framework\Setup\SchemaSetupInterface;

class InstallSchema implements InstallSchemaInterface
{

    public function install(
        SchemaSetupInterface $setup,
        ModuleContextInterface $context
    ) {
        $installer = $setup;
        $installer->startSetup();
        //Create ithinklogistics_credential table
		if (!$installer->tableExists('ithinklogistics_credential')) {
			$table = $installer->getConnection()->newTable(
				$installer->getTable('ithinklogistics_credential')
			)
                ->addColumn(
                    'c_id',
                    \Magento\Framework\DB\Ddl\Table::TYPE_INTEGER,
                    null,
                    [
                        'identity' => true,
                        'unsigned' => true,
                        'nullable' => false,
                        'primary' => true
                    ],
                    'ID'
                )
                ->addColumn(
                    'access_token',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Access Token'
                )
                ->addColumn(
                    'secret_key',
                    \Magento\Framework\DB\Ddl\Table::TYPE_TEXT,
                    null,
                    ['nullable' => false, 'default' => ''],
                    'Secret Key'
                )
                ->setComment('Ithinklogistics Table');
                $installer->getConnection()->createTable($table);
        }
        $installer->endSetup();     
    }
}
