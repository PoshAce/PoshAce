<?php

namespace Magetrend\Email\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


class StrictDirectiveTemplate implements DataPatchInterface
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
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
    }

    /**
     * @inheritDoc
     */
    public function apply()
    {

        $this->moduleDataSetup->getConnection()->startSetup();
        $dbTable = $this->moduleDataSetup->getTable('email_template');

        $select = $this->moduleDataSetup->getConnection()->select()
            ->from(
                $dbTable,
                ['template_id', 'template_subject']
            )
            ->where('is_mt_email = ?', 1);

        $emailCollection = $this->moduleDataSetup->getConnection()->fetchAll($select);
        if (!empty($emailCollection)) {
            foreach ($emailCollection as $emailTemplate) {
                $id = $emailTemplate['template_id'];
                $subject = $this->convertToStrictMode($emailTemplate['template_subject']);
                $this->moduleDataSetup->getConnection()->update(
                    $dbTable,
                    [
                        'template_subject' => $subject
                    ],
                    ['template_id =?' => (int)$id]
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
