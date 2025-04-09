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


namespace Mirasvit\Feed\Model\ResourceModel\Rule;

use Magento\Framework\Data\Collection\Db\FetchStrategyInterface;
use Magento\Framework\Data\Collection\EntityFactoryInterface;
use Magento\Framework\DB\Adapter\AdapterInterface;
use Magento\Framework\Event\ManagerInterface;
use Magento\Framework\Model\ResourceModel\Db\AbstractDb;
use Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection;
use Mirasvit\Feed\Model\ResourceModel\Rule;
use Mirasvit\Feed\Repository\RuleRepository;
use Mirasvit\Feed\Service\Rule\ToStringService;
use Psr\Log\LoggerInterface;

class Collection extends AbstractCollection
{
    protected $toStringService;

    protected $ruleRepository;

    public function __construct(
        RuleRepository         $ruleRepository,
        ToStringService        $toStringService,
        EntityFactoryInterface $entityFactory,
        LoggerInterface        $logger,
        FetchStrategyInterface $fetchStrategy,
        ManagerInterface       $eventManager,
        AdapterInterface       $connection = null,
        AbstractDb             $resource = null
    ) {
        $this->ruleRepository  = $ruleRepository;
        $this->toStringService = $toStringService;
        parent::__construct($entityFactory, $logger, $fetchStrategy, $eventManager, $connection, $resource);
    }

    protected function _construct()
    {
        $this->_init(
            \Mirasvit\Feed\Model\Rule::class,
            Rule::class
        );
    }

    public function toOptionArray(): array
    {
        $data = [];
        foreach ($this->ruleRepository->getCollection() as $rule) {
            $data[] = [
                'value'      => $rule->getId(),
                'label'      => $rule->getName(),
                'conditions' => $this->toStringService->toString($rule),
            ];
        }

        return $data;
    }
}
