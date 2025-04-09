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

namespace Mirasvit\Feed\Model\Feed;

use Exception;
use Generator;
use Magento\Framework\Event\ManagerInterface as EventManager;
use Magento\Framework\Stdlib\DateTime;
use Magento\Store\Model\App\EmulationFactory;
use Magento\Store\Model\App\Emulation;
use Mirasvit\Feed\Export\Handler;
use Mirasvit\Feed\Export\HandlerFactory;
use Mirasvit\Feed\Export\Step\Exporting;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Helper\LockManager;

/**
 * @SuppressWarnings(PHPMD.CouplingBetweenObjects)
 */
class Exporter
{

    protected $productExportStep = 0;

    protected $appEmulationFactory;

    protected $appEmulation;

    protected $handlerFactory;

    protected $history;

    protected $config;

    protected $lockManager;

    private   $eventManager;

    protected $handlers          = [];

    public function __construct(
        EmulationFactory $appEmulationFactory,
        EventManager     $eventManager,
        HandlerFactory   $handlerFactory,
        History          $history,
        Config           $config,
        LockManager      $lockManager
    ) {
        $this->appEmulationFactory = $appEmulationFactory;
        $this->eventManager        = $eventManager;
        $this->handlerFactory      = $handlerFactory;
        $this->history             = $history;
        $this->config              = $config;
        $this->lockManager         = $lockManager;
    }

    public function setProductExportStep(int $step): self
    {
        $this->productExportStep = $step;

        return $this;
    }

    public function getHandler(Feed $feed): Handler
    {
        if (!isset($this->handlers[$feed->getId()])) {
            $this->handlers[$feed->getId()] = $this->handlerFactory->create()
                ->setProductExportStep($this->productExportStep)
                ->setFeed($feed);
        }

        return $this->handlers[$feed->getId()];
    }

    public function export(Feed $feed): string
    {
        register_shutdown_function([$this, 'onShutdown'], $feed);

        $this->startEmulation($feed);

        $handler = $this->getHandler($feed);
        $handler->setFilename($feed->getFilename());

        $handler->execute();

        $this->updateFeed($feed, $handler);

        $this->stopEmulation();

        return $handler->getStatus();
    }


    public function exportCli(Feed $feed): Generator
    {
        $lockName    = $feed->getId() . '.cli.lock';
        $lockFile    = $this->config->getTmpPath() . '/' . $lockName;
        $lockPointer = fopen($lockFile, "w");

        if ($this->lockManager->lock($lockName, $lockFile)) {
            register_shutdown_function([$this, 'onShutdown'], $feed);

            $this->history->add($feed, (string)__('Export'), (string)__('Start export process'));

            $this->startEmulation($feed);

            $handler = $this->getHandler($feed);

            $handler->reset()
                ->setFilename($feed->getFilename());

            do {
                $handler->execute();

                $this->updateFeed($feed, $handler);

                yield $handler->getStatus() => $handler->toString();
            } while (!in_array($handler->getStatus(), [
                Config::STATUS_COMPLETED,
                Config::STATUS_ERROR,
            ]));

            $this->stopEmulation();

            $this->lockManager->unlock($lockName);

        } else {
            throw new Exception("File $lockFile already locked by another process");
        }

        fclose($lockPointer);
    }

    public function exportPreview(Feed $feed)
    {
        $this->startEmulation($feed);

        $handler = $this->getHandler($feed);

        $handler->reset();

        $handler->setFilename($feed->getPreviewFilename())
            ->enableTestMode();

        do {
            $handler->execute();
        } while (!in_array($handler->getStatus(), [
            Config::STATUS_COMPLETED,
            Config::STATUS_ERROR,
        ]));

        $this->stopEmulation();
    }

    protected function updateFeed(Feed $feed, Handler $handler): self
    {
        $this->history->add($feed, (string)__('Export'), $handler->toString());

        if ($handler->getStatus() == Config::STATUS_COMPLETED) {
            $feed->setGeneratedAt((new \DateTime())->format(DateTime::DATETIME_PHP_FORMAT))
                ->setGeneratedTime($handler->getTimeSinceStart())
                ->setGeneratedCnt($handler->getStepData(Exporting::STEP, 'data/count'))
                ->save();

            $this->history->add($feed, (string)__('Export'), (string)__('Feed was successfully exported'));

            $this->eventManager->dispatch('feed_export_success', ['feed' => $feed]);
        }

        return $this;
    }

    public function onShutdown($feed)
    {
        if (error_get_last()) {
            $error = error_get_last();
            if ($error['type'] === E_ERROR) {
                $message = $error['message'];
                $this->history->add($feed, (string)__('Error'), $message);
                $this->eventManager->dispatch('feed_export_fail', ['feed' => $feed, 'error' => $message]);
            }
        }
    }

    private function startEmulation(Feed $feed)
    {
        if (!$this->appEmulation) {
            $this->appEmulation = $this->appEmulationFactory->create();
        }

        $this->appEmulation->startEnvironmentEmulation($feed->getStore()->getId());
        $_SERVER['FEED_STORE_ID'] = $feed->getStore()->getId();
    }

    private function stopEmulation()
    {
        $this->appEmulation->stopEnvironmentEmulation();
    }
}
