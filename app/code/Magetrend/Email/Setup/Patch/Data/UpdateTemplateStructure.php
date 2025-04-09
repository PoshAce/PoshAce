<?php

namespace Magetrend\Email\Setup\Patch\Data;

use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Framework\Setup\Patch\DataPatchInterface;


class UpdateTemplateStructure implements DataPatchInterface
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
                ['template_id', 'template_text', 'orig_template_variables']
            )
            ->where('is_mt_email = ?', 1)
            ->where('template_text like ?', '%template_id=$this.id%');

        $emailCollection = $this->moduleDataSetup->getConnection()->fetchAll($select);
        if (!empty($emailCollection)) {
            foreach ($emailCollection as $emailTemplate) {
                $id = $emailTemplate['template_id'];

                $text = $emailTemplate['template_text'];
                $newText = str_replace('template_id=$this.id', 'template_id=$this.template_id', $text);

                $vars = $emailTemplate['orig_template_variables'];
                $newVars = str_replace('template_id=$this.id', 'template_id=$this.template_id', $vars);

                $this->moduleDataSetup->getConnection()->update(
                    $dbTable,
                    [
                        'template_text' => $newText,
                        'orig_template_variables' => $newVars
                    ],
                    ['template_id =?' => (int)$id]
                );
            }
        }

        $this->moduleDataSetup->getConnection()->endSetup();
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
