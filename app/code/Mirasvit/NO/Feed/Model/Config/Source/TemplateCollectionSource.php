<?php
/**
 * Mirasvit
 *
 * This source file is subject to the Mirasvit Software License, which is available at https://mirasvit.com/license/.
 * Do not edit or add to this file if you wish to upgrade the to newer versions in the future.
 * If you wish to customize this module for your needs.
 * Please refer to http://www.magentocommerce.com for more information.
 *
 * @category  Mirasvit
 * @package   mirasvit/module-feed
 * @version   1.4.6
 * @copyright Copyright (C) 2024 Mirasvit (https://mirasvit.com/)
 */



namespace Mirasvit\Feed\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Feed\Model\ResourceModel\Template\CollectionFactory as TemplateCollectionFactory;

class TemplateCollectionSource implements OptionSourceInterface
{

    protected $collectionFactory;

    protected $config;

    public function __construct(
        TemplateCollectionFactory $collectionFactory
    ) {
        $this->collectionFactory = $collectionFactory;
    }

    public function toOptionArray(): array
    {
        $data = [];
        foreach ($this->collectionFactory->create() as $template) {
            $data[] = [
                'value' => $template->getId(),
                'label' => $template->getName() . '  (' . $template->getType() . ')',
            ];
        }

        return $data;
    }
}
