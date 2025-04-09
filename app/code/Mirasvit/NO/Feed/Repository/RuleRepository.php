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

namespace Mirasvit\Feed\Repository;

use Magento\Framework\App\ResourceConnection;
use Magento\Framework\EntityManager\EntityManager;
use Mirasvit\Core\Service\SerializeService;
use Mirasvit\Feed\Api\Data\RuleInterface;
use Mirasvit\Feed\Api\Data\RuleInterfaceFactory;
use Mirasvit\Feed\Model\ResourceModel\Rule\Collection;
use Mirasvit\Feed\Model\ResourceModel\Rule\CollectionFactory;
use Mirasvit\Feed\Model\Rule\RuleFactory;
use Mirasvit\Feed\Model\Rule\Rule;

class RuleRepository
{
    private $factory;

    private $collectionFactory;

    private $entityManager;

    private $ruleFactory;

    private $resource;

    public function __construct(
        RuleInterfaceFactory $factory,
        CollectionFactory    $collectionFactory,
        EntityManager        $entityManager,
        RuleFactory          $ruleFactory,
        ResourceConnection   $resource
    ) {
        $this->factory           = $factory;
        $this->collectionFactory = $collectionFactory;
        $this->entityManager     = $entityManager;
        $this->ruleFactory       = $ruleFactory;
        $this->resource          = $resource;
    }

    /**
     * @return RuleInterface[]|Collection
     */
    public function getCollection()
    {
        return $this->collectionFactory->create();
    }

    public function create(): RuleInterface
    {
        return $this->factory->create();
    }

    public function get(int $id): ?RuleInterface
    {
        $model = $this->create();
        $model = $this->entityManager->load($model, $id);

        return $model->getId() ? $model : null;
    }

    public function save(RuleInterface $model): RuleInterface
    {
        return $this->entityManager->save($model);
    }

    public function delete(RuleInterface $model)
    {
        $this->entityManager->delete($model);
    }

    public function createRuleInstance(): Rule
    {
        return $this->ruleFactory->create();
    }

    public function getRuleInstance(RuleInterface $model): Rule
    {
        $rule = $this->createRuleInstance();
        $rule->getConditions()->loadArray(
            SerializeService::decode($model->getConditionsSerialized())
        );

        return $rule;
    }

    public function getFeedIds(RuleInterface $model): array
    {
        $connection = $this->resource->getConnection();

        $select = $connection->select()
            ->from($this->resource->getTableName(RuleInterface::REL_FEED_TABLE_NAME), ['feed_id'])
            ->where('rule_id = :rule_id');

        return $connection->fetchCol($select, [':rule_id' => $model->getId()]);
    }

    public function saveFeedIds(RuleInterface $model, array $feedIds)
    {
        $connection = $this->resource->getConnection();
        $table      = $this->resource->getTableName(RuleInterface::REL_FEED_TABLE_NAME);

        $connection->delete($table, ['rule_id = ?' => $model->getId()]);

        foreach ($feedIds as $feedId) {
            $connection->insert($table, [
                'rule_id' => $model->getId(),
                'feed_id' => $feedId,
            ]);
        }
    }

    public function getProductIds(RuleInterface $model): array
    {
        $connection = $this->resource->getConnection();

        $select = $connection->select()
            ->from($this->resource->getTableName(RuleInterface::REL_PRODUCT_TABLE_NAME), ['feed_id'])
            ->where('rule_id = :rule_id');

        return $connection->fetchCol($select, [':rule_id' => $model->getId()]);
    }

    public function clearProductIds(RuleInterface $model)
    {
        $connection = $this->resource->getConnection();
        $table      = $this->resource->getTableName(RuleInterface::REL_PRODUCT_TABLE_NAME);

        $connection->delete($table, ['rule_id = ?' => $model->getId()]);
    }

    public function addProductIds(RuleInterface $model, array $productIds)
    {
        $connection = $this->resource->getConnection();
        $table      = $this->resource->getTableName(RuleInterface::REL_PRODUCT_TABLE_NAME);

        foreach ($productIds as $productId) {
            $connection->insert($table, [
                'rule_id'    => $model->getId(),
                'product_id' => $productId,
            ]);
        }
    }
}
