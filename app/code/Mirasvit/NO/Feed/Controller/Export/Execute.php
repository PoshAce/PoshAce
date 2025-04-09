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

namespace Mirasvit\Feed\Controller\Export;

use Exception;
use Magento\Framework\App\Response\Http\Interceptor;
use Mirasvit\Feed\Controller\Export;
use Mirasvit\Feed\Export\Step\Exporting as ExportingStep;
use Mirasvit\Feed\Export\Step\Validation as ValidationStep;
use Mirasvit\Feed\Model\Config;

class Execute extends Export
{
    public function execute()
    {
        $mode = $this->getRequest()->getParam('mode');

        try {
            $feed    = $this->getFeed();
            $handler = $this->exporter->getHandler($feed);

            if (empty($feed['filename'])) {
                throw new Exception("Please set and save the feed <b>Filename</b> before generation");
            }

            $result = [
                'success'  => true,
                'progress' => [],
            ];

            if (!$mode || $mode == 'new') {
                $handler->reset();
                $result['progress'] = $handler->toJson();
            } else {
                $status = $this->exporter->export($feed);

                $result['status'] = $status;

                if ($status == Config::STATUS_COMPLETED) {
                    $result['progress']['completed'] = [
                        'url'   => $feed->getUrl(),
                        'time'  => gmdate('H:i:s', (int)$feed->getGeneratedTime()),
                        'count' => $handler->getStepData(ExportingStep::STEP, 'data/count'),
                        'valid' => $handler->getStepData(ExportingStep::STEP, 'data/count') - $handler->getStepData(
                                ValidationStep::STEP,
                                'data/' . ValidationStep::INVALID_ENTITY_COUNT
                            ),
                    ];
                } else {
                    $result['progress'] = $handler->toJson();
                }
            }
        } catch (Exception $e) {
            $result['success']           = false;
            $result['progress']['error'] = $e->getMessage();
        }

        $callback = $this->getRequest()->getParam('callback');

        /** @var Interceptor $response */
        $response = $this->getResponse();
        $response->setHeader('Content-Type', 'application/javascript', true);
        $response->setBody($callback . '(' . $this->serializer->serialize($result) . ')');
    }

    protected function _processUrlKeys(): bool
    {
        return true;
    }
}
