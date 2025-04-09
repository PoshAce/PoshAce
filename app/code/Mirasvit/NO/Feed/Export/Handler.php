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

namespace Mirasvit\Feed\Export;

use Magento\Framework\DataObject;
use Mirasvit\Feed\Model\Feed;

class Handler
{

    protected $productExportStep = 0;

    private   $context;

    public function __construct(
        Context $context
    ) {
        $this->context = $context;
    }

    public function setProductExportStep(int $step): self
    {
        $this->productExportStep = $step;

        return $this;
    }

    public function setFeed(Feed $feed): self
    {
        $this->context->setFeed($feed);
        $this->context->load();

        return $this;
    }

    public function execute(): string
    {
        $this->context->setProductExportStep($this->productExportStep);
        $this->context->execute();

        return $this->getStatus();
    }


    public function setFilename(string $file): self
    {
        $this->context->setFilename($file);

        return $this;
    }

    public function enableTestMode(): self
    {
        $this->context->enableTestMode();

        return $this;
    }

    public function reset(): self
    {
        $this->context->reset();

        return $this;
    }

    public function getStatus(): string
    {
        return $this->context->getRootStep()->getStatus();
    }

    public function getTimeSinceStart(): int
    {
        return (int)(microtime(true) - $this->context->getCreatedAt());
    }

    public function toJson(): array
    {
        return $this->context->getRootStep()->toJson();
    }

    public function toString(): string
    {
        return $this->context->getRootStep()->toString();
    }

    public function toArray(): array
    {
        return $this->context->getRootStep()->toArray();
    }

    public function getStepData(string $stepName, string $key)
    {
        $value = null;
        $state = $this->toArray();

        if (is_array($state) && isset($state['steps']) && is_array($state['steps'])) {
            foreach ($state['steps'] as $step) {
                $step = new DataObject($step);
                if (strtolower($stepName) === strtolower((string)$step->getData('name'))) {
                    $value = $step->getData($key);
                }
            }
        }

        return $value;
    }
}
