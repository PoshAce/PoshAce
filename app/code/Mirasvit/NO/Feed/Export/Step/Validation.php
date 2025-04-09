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

use Magento\Framework\App\ResourceConnection;
use Mirasvit\Feed\Api\Data\ValidationInterface;
use Mirasvit\Feed\Repository\ValidationRepository;
use Mirasvit\Feed\Export\Context;
use Mirasvit\Core\Helper\Io;
use Mirasvit\Feed\Model\Feed;

class Validation extends AbstractStep
{
    const CSV                  = 'csv';
    const XML                  = 'xml';
    const STEP                 = 'validation';
    const INVALID_ENTITY_COUNT = 'invalid_entity_count';

    protected $io;

    private   $validationRepository;

    private   $invalidEntityQty = 0;

    private   $resource;

    public function __construct(
        ValidationRepository $validationRepository,
        ResourceConnection   $resource,
        Io                   $io,
        Context              $context
    ) {
        $this->validationRepository = $validationRepository;
        $this->resource             = $resource;
        $this->io                   = $io;

        parent::__construct($context);
    }

    public function beforeExecute()
    {
        $connection = $this->resource->getConnection();
        $connection->delete(
            $this->resource->getTableName(ValidationInterface::TABLE_NAME),
            [ValidationInterface::FEED_ID . ' = ?' => $this->context->getFeed()->getId()]
        );

        return parent::beforeExecute();
    }

    public function execute()
    {
        if ($this->context->isTestMode()) {
            return parent::execute();
        }

        if ($this->isReady()) {
            $this->beforeExecute();
        }

        $feed = $this->context->getFeed();

        $schemaValidationService = $this->validationRepository->getSchemaValidationService(
            $feed->isXml() ? self::XML : self::CSV
        )->init($feed);

        $result                 = $schemaValidationService->validateSchema();
        $this->invalidEntityQty = $schemaValidationService->getInvalidEntityQty();

        if ($result) {
            $connection = $this->resource->getConnection();
            $connection->insertMultiple($this->resource->getTableName(ValidationInterface::TABLE_NAME), $result);
        }

        $this->index++;

        if ($this->isCompleted()) {
            $this->afterExecute();
        }

        return $this;
    }

    public function afterExecute(): self
    {
        $this->setData(self::INVALID_ENTITY_COUNT, $this->invalidEntityQty);

        return $this;
    }
}
