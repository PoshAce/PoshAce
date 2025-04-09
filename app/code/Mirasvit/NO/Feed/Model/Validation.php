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

namespace Mirasvit\Feed\Model;


use Magento\Framework\Model\AbstractModel;
use Mirasvit\Feed\Api\Data\ValidationInterface;

class Validation extends AbstractModel implements ValidationInterface
{
    protected $_eventPrefix = 'feed_validation';

    protected function _construct()
    {
        $this->_init(ResourceModel\Validation::class);
    }

    public function getValidator(): string
    {
        return $this->getData(self::VALIDATOR);
    }
}
