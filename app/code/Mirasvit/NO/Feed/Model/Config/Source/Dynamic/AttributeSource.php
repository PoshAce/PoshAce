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


declare(strict_types=1);

namespace Mirasvit\Feed\Model\Config\Source\Dynamic;

use Magento\Framework\Data\OptionSourceInterface;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Model\ResourceModel\Dynamic\Attribute\CollectionFactory as AttributeCollectionFactory;

class AttributeSource implements OptionSourceInterface
{
    protected $attributeCollectionFactory;

    protected $config;

    public function __construct(
        AttributeCollectionFactory $attributeCollectionFactory,
        Config                     $config
    ) {
        $this->attributeCollectionFactory = $attributeCollectionFactory;
        $this->config                     = $config;
    }

    public function toOptionArray(): array
    {
        $result = [];

        $path = $this->config->getDynamicAttributePath();
        if ($handle = opendir($this->config->absolutePath($path))) {
            while (false !== ($entry = readdir($handle))) {
                if (substr($entry, 0, 1) != '.') {
                    $result[] = [
                        'label' => $entry,
                        'value' => $path . '/' . $entry,
                    ];
                }
            }
            closedir($handle);
        }

        sort($result);

        return $result;
    }
}
