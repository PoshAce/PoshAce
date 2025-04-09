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

namespace Mirasvit\Feed\Model\ResourceModel;

use Magento\Framework\Model\AbstractModel;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Stdlib\DateTime;

class Feed extends AbstractDb
{
    protected function _construct()
    {
        $this->_init('mst_feed_feed', 'feed_id');
    }

    protected function _beforeSave(AbstractModel $object)
    {
        /** @var \Mirasvit\Feed\Model\Feed $object */

        if ($object->isObjectNew() && !$object->hasCreatedAt()) {
            $object->setCreatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));
        }

        $object->setUpdatedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT));

        if (!$object->getIsMassStatus()) {
            if (is_array($object->getCronDay())) {
                $object->setCronDay(implode(',', $object->getCronDay()));
            }
            if (is_array($object->getCronTime())) {
                $object->setCronTime(implode(',', $object->getCronTime()));
            }
            if (is_array($object->getNotificationEvents())) {
                $object->setNotificationEvents(implode(',', $object->getNotificationEvents()));
            }
        }

        return parent::_beforeSave($object);
    }

    protected function _afterSave(AbstractModel $object)
    {
        $this->saveRules($object);

        return parent::_afterSave($object);
    }

    protected function _afterLoad(AbstractModel $object)
    {
        $this->loadRuleIds($object);

        return parent::_afterLoad($object);
    }

    public function loadRuleIds(AbstractModel $object): AbstractModel
    {
        $select = $this->getConnection()->select()
            ->from($this->getTable('mst_feed_rule_feed'))
            ->where('feed_id = ?', $object->getId());

        if ($data = $this->getConnection()->fetchAll($select)) {
            $array = [];
            foreach ($data as $row) {
                $array[] = $row['rule_id'];
            }
            $object->setData('rule_ids', $array);
        }

        return $object;
    }

    protected function saveRules(AbstractModel $object): AbstractModel
    {
        $table     = $this->getTable('mst_feed_rule_feed');
        $condition = $this->getConnection()->quoteInto('feed_id = ?', $object->getId());

        $this->getConnection()->delete($table, $condition);

        foreach ((array)$object->getData('rule_ids') as $ruleId) {
            $insertArray = [
                'feed_id' => $object->getId(),
                'rule_id' => $ruleId,
            ];
            $this->getConnection()->insert($table, $insertArray);
        }

        return $object;
    }

}
