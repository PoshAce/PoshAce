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


namespace Mirasvit\Feed\Model\ResourceModel\Dynamic;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Context;
use Magento\Framework\Stdlib\DateTime;
use Mirasvit\Feed\Service\Serialize;

class Category extends AbstractDb
{

    protected $serializer;

    public function __construct(
        Serialize $serializer,
        Context   $context
    ) {
        $this->serializer = $serializer;

        parent::__construct($context);
    }

    protected function _construct()
    {
        $this->_init('mst_feed_mapping_category', 'mapping_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        if ($object->getData('mapping') && is_array($object->getData('mapping'))) {
            $object->setData('mapping_serialized', $this->serializer->serialize($object->getData('mapping')));
        }

        return parent::_beforeSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        if ($object->getData('mapping_serialized')) {
            $object->setData('mapping', $this->serializer->unserialize($object->getData('mapping_serialized')));
        }

        return parent::_afterLoad($object);
    }
}
