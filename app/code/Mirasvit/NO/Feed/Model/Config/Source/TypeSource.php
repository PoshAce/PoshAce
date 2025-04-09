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


namespace Mirasvit\Feed\Model\Config\Source;

use Magento\Framework\Data\OptionSourceInterface;

class TypeSource implements OptionSourceInterface
{
    public function toOptionArray(): array
    {
        return [
            [
                'label' => 'CSV',
                'value' => 'csv',
            ],
            [
                'label' => 'TXT',
                'value' => 'txt',
            ],
            [
                'label' => 'XML',
                'value' => 'xml',
            ],
        ];
    }
}
