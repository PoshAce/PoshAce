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

class Root extends AbstractStep
{

    private $stepFactory;

    public function __construct(
        Context $context
    ) {
        $this->context     = $context;
        $this->stepFactory = $context->getStepFactory();

        parent::__construct($context);
    }

    public function beforeExecute()
    {
        $this->addStep(
            $this->stepFactory->create('Initialization')
                ->setName((string)__('Initialization'))
        );

        $this->addStep(
            $this->stepFactory->create('Filtration')
                ->setName((string)__('Filtration'))
        );

        $this->addStep(
            $this->stepFactory->create('Exporting')
                ->setName((string)__('Exporting'))
        );

        $this->addStep(
            $this->stepFactory->create('Finalization')
                ->setName((string)__('Finalization'))
        );

        $this->addStep(
            $this->stepFactory->create('Validation')
                ->setName((string)__('Validation'))
        );

        return parent::beforeExecute();
    }

    /**
     * @SuppressWarnings(PHPMD.CyclomaticComplexity) 
     */
    public function toJson(): array
    {
        $result = [];

        $steps = $this->getSteps();

        $prevStatus = '';
        foreach ($steps as $step) {
            $status = $step->getStatus();

            if ($status == Config::STATUS_READY && $prevStatus == Config::STATUS_COMPLETED) {
                $status = Config::STATUS_PROCESSING;
            }

            $result['steps'][] = [
                'name'   => $step->getName(),
                'status' => $status,
            ];

            $prevStatus = $status;

            if (count($step->getSteps())) {
                foreach ($step->getSteps() as $subStep) {
                    if ($subStep->isProcessing()) {
                        if ($subStep->getIndex() && $subStep->getLength()) {
                            $result['current'] = [
                                'name'     => $subStep->getName(),
                                'length'   => $subStep->getLength(),
                                'index'    => $subStep->getIndex(),
                                'position' => __('%1 out of %2', $subStep->getIndex(), $subStep->getLength()),
                                'eta'      => $this->getEta(
                                    $subStep->getIndex(),
                                    $subStep->getLength(),
                                    $subStep->getStartedAt()
                                ),
                                'percent'  => ($subStep->getIndex() / $subStep->getLength() * 100) . '%',
                            ];
                        }
                    }
                }
            } else {
                if ($step->isProcessing()) {
                    $result['current'] = [
                        'name'     => $step->getName(),
                        'length'   => $step->getLength(),
                        'index'    => $step->getIndex(),
                        'position' => __('%1 out of %2', $step->getIndex(), $step->getLength()),
                        'eta'      => $this->getEta($step->getIndex(), $step->getLength(), $step->getStartedAt()),
                        'percent'  => ($step->getIndex() / $step->getLength() * 100) . '%',
                    ];
                }
            }
        }

        return $result;
    }

    public function getEta(int $position, int $size, int $startTime): ?string
    {
        $percent = $position / $size;
        if ($percent > 0) {
            $eta = (int)(((microtime(true) - $startTime) / $percent) * (1 - $percent));
            if ($eta > 3600) {
                $etaMsg = date('h:i:s', $eta);
            } else {
                $etaMsg = date('i:s', $eta);
            }

            return __('ETA %1', $etaMsg)->__toString();
        }

        return null;
    }
}
