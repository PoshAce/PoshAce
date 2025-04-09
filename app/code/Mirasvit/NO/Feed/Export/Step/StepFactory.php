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

namespace Mirasvit\Feed\Export\Step;

use Magento\Framework\ObjectManagerInterface;
use Mirasvit\Feed\Export\Step\AbstractStep;

class StepFactory
{
    protected $objectManager;

    public function __construct(ObjectManagerInterface $objectManager)
    {
        $this->objectManager = $objectManager;
    }

    public function create(string $className, array $data = []): AbstractStep
    {
        if (strpos($className, 'Mirasvit') === false) {
            $className = 'Mirasvit\Feed\Export\Step\\' . $className;
        }

        $step = $this->objectManager->create($className, $data);

        return $step;
    }
}
