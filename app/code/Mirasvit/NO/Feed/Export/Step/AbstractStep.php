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

use Mirasvit\Feed\Export\Context;
use Mirasvit\Feed\Model\Config;

abstract class AbstractStep
{

    protected $name;

    protected $length    = 1;

    protected $index     = -1;

    protected $startedAt = null;

    protected $data      = [];

    protected $context;

    protected $steps     = [];

    public function __construct(
        Context $context,
                $data = []
    ) {
        $this->context = $context;
        $this->data    = $data;
    }

    public function beforeExecute()
    {
        $this->index     = 0;
        $this->startedAt = microtime(true);

        if (count($this->steps)) {
            $this->length = count($this->steps);
        }

        return $this;
    }

    public function execute()
    {
        if ($this->isReady()) {
            $this->beforeExecute();
        }

        if (count($this->steps)) {
            foreach ($this->steps as $step) {
                while (!$step->isCompleted()) {
                    $step->execute();

                    if ($step->isCompleted()) {
                        $this->index++;
                    }

                    $this->context->save();

                    if ($this->context->isTimeout()) {
                        break 2;
                    }
                }
            }
        } else {
            $this->index++;
            $this->context->save();
        }

        if ($this->isCompleted()) {
            $this->afterExecute();
        }

        return $this;
    }

    public function afterExecute()
    {
        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function isReady(): bool
    {
        return $this->index == -1;
    }

    public function isCompleted(): bool
    {
        return $this->index >= $this->length;
    }

    public function isProcessing(): bool
    {
        return !$this->isCompleted() && $this->index >= 0;
    }

    public function getStatus(): string
    {
        if ($this->isReady()) {
            return Config::STATUS_READY;
        } elseif ($this->isCompleted()) {
            return Config::STATUS_COMPLETED;
        }

        return Config::STATUS_PROCESSING;
    }

    public function addStep(self $step): self
    {
        $this->steps[] = $step;

        return $this;
    }

    public function getSteps(): array
    {
        return $this->steps;
    }

    public function setData(string $key, $value): self
    {
        $this->data[$key] = $value;

        $this->context->save();

        return $this;
    }

    public function getData(string $key = null): ?array
    {
        if ($key == null) {
            return $this->data;
        }

        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return null;
    }

    public function getIndex(): int
    {
        return $this->index;
    }

    public function getLength(): int
    {
        return $this->length;
    }

    public function getStartedAt(): int
    {
        return (int)$this->startedAt;
    }


    public function toString(): string
    {
        $string = '';
        if ($this->name) {
            $string .= $this->name . '...';
        }

        $string .= $this->index . '  / ' . $this->length;

        foreach ($this->steps as $step) {
            $string .= PHP_EOL . '  ' . $step->toString();
        }

        return $string;
    }

    public function toArray(): array
    {
        $array = [
            'class'     => get_class($this),
            'index'     => $this->index,
            'length'    => $this->length,
            'startedAt' => $this->startedAt,
            'name'      => $this->name,
            'data'      => $this->data,
            'steps'     => [],
        ];

        foreach ($this->steps as $step) {
            $array['steps'][] = $step->toArray();
        }

        return $array;
    }

    public function fromArray(array $data): self
    {
        $this->index     = $data['index'];
        $this->length    = $data['length'];
        $this->data      = $data['data'];
        $this->name      = $data['name'];
        $this->startedAt = $data['startedAt'];

        foreach ($data['steps'] as $stepData) {
            $step          = $this->context->getStepFactory()->create($stepData['class'], ['data' => $stepData['data']]);
            $this->steps[] = $step->fromArray($stepData);
        }

        return $this;
    }
}
