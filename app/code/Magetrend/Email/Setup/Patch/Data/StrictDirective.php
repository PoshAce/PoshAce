<?php

namespace Magetrend\Email\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


class StrictDirective implements DataPatchInterface
{
    /**
     * @var ModuleDataSetupInterface
     */
    private $moduleDataSetup;

    /**
     * @param ModuleDataSetupInterface $moduleDataSetup
     */
    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup
    )
    {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {
        $this->moduleDataSetup->getConnection()->startSetup();
        $varTable = $this->moduleDataSetup->getTable('mt_email_var');


        $select = $this->moduleDataSetup->getConnection()->select()->from(
            $varTable,
            ['entity_id', 'var_value']
        );

        $varCollection = $this->moduleDataSetup->getConnection()->fetchAll($select);
        if (!empty($varCollection)) {
            foreach ($varCollection as $variable) {
                $value = $variable['var_value'];
                $id = $variable['entity_id'];

                $newVarValue = $this->convertToStrictMode($value);
                $this->moduleDataSetup->getConnection()->update(
                    $varTable,
                    ['var_value' => $newVarValue],
                    ['entity_id = ?' => (int)$id]
                );
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
    }

    public function convertToStrictMode($content)
    {
        $listOfFixes = $this->getReplacementList();
        if (empty($listOfFixes) || empty($content)) {
            return $content;
        }

        $content = str_replace(array_keys($listOfFixes), array_values($listOfFixes), $content);
        return $content;
    }

    public function getReplacementList()
    {
        $list = [
            'store.getFrontendName()' => 'store.frontend_name',
            'order.getCreatedAtFormatted(2)' => 'created_at_formatted',
            'order.getCustomerName()' => 'order_data.customer_name',
            'billing.getName()' => 'billing.firstname',
            'subscriber.getConfirmationLink()' => 'subscriber_data.confirmation_link',
            "store.getUrl('')" => 'store_url',
            'order.getStatusLabel()' => 'order_data.frontend_status_label',
            'order.getIncrementId()' => 'order.increment_id'
        ];

        return $list;
    }

    /**
     * @inheritDoc
     */
    public static function getDependencies()
    {
        return [];
    }

    /**
     * @inheritDoc
     */
    public function getAliases()
    {
        return [];
    }
}