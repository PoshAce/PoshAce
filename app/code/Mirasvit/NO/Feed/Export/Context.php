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

use Mirasvit\Feed\Export\Step\StepFactory;
use Mirasvit\Core\Helper\Io;
use Mirasvit\Feed\Model\Config;
use Mirasvit\Feed\Service\Serialize;
use Mirasvit\Feed\Model\Feed;
use Mirasvit\Feed\Export\Step\Root;

class Context
{
    protected $productExportStep = 0;

    protected $feed;

    protected $rootStep;

    protected $filename;

    protected $isTestMode        = false;

    protected $currentObject;

    protected $serializer;

    private   $lastSaveTime;

    private   $startedAt;

    private   $createdAt;

    private   $stepFactory;

    private   $config;

    private   $io;

    public function __construct(
        Io          $io,
        Config      $config,
        StepFactory $stepFactory,
        Serialize   $serializer
    ) {
        $this->io          = $io;
        $this->config      = $config;
        $this->stepFactory = $stepFactory;
        $this->serializer  = $serializer;

        $this->createdAt = microtime(true);
        $this->startedAt = microtime(true);

        $this->lastSaveTime = 0;
    }

    public function setProductExportStep(int $step): self
    {
        $this->productExportStep = $step;

        return $this;
    }


    public function getProductExportStep(): int
    {
        return $this->productExportStep;
    }

    /**
     * @return Step\StepFactory
     */
    public function getStepFactory()
    {
        return $this->stepFactory;
    }


    public function setFeed(Feed $feed): self
    {
        $this->feed = $feed;

        return $this;
    }

    public function getFeed(): ?Feed
    {
        return $this->feed;
    }

    public function setFilename(string $file): self
    {
        $this->filename = $file;

        return $this;
    }

    public function getFilename(): string
    {
        return $this->filename;
    }


    public function setCurrentObject(object $obj): self
    {
        $this->currentObject = $obj;

        return $this;
    }

    public function getCurrentObject(): object
    {
        return $this->currentObject;
    }

    public function enableTestMode(): self
    {
        $this->isTestMode = true;

        return $this;
    }

    public function isTestMode(): bool
    {
        return $this->isTestMode;
    }

    public function getRootStep(): Root
    {
        return $this->rootStep;
    }

    public function execute(): self
    {
        $this->startedAt = microtime(true);

        $this->getRootStep()->execute();

        return $this;
    }

    public function isTimeout(): bool
    {
        if (microtime(true) - $this->lastSaveTime > 0.9) {
            $this->save();
            $this->lastSaveTime = microtime(true);
        }

        $isTimeout = microtime(true) - $this->startedAt > $this->config->getMaxAllowedTime();

        return $isTimeout;
    }


    public function toString(): string
    {
        return $this->rootStep->toString() . PHP_EOL;
    }

    public function reset(): self
    {
        $this->rootStep   = $this->stepFactory->create('Root');
        $this->createdAt  = microtime(true);
        $this->filename   = null;
        $this->isTestMode = false;

        $this->save();

        return $this;
    }

    public function save(): self
    {
        $data = $this->rootStep->toArray();

        $data['filename']   = $this->filename;
        $data['isTestMode'] = $this->isTestMode;
        $data['createdAt']  = $this->createdAt;

        $string = $this->serializer->serialize($data);

        $this->io->write($this->getStateFile(), $string);

        return $this;
    }

    public function load(): self
    {
        $this->rootStep = $this->stepFactory->create('Root');

        if ($this->io->fileExists($this->getStateFile()) && ($data = $this->io->fileGetContents($this->getStateFile()))) {
            $data             = $this->serializer->unserialize($data);
            $this->filename   = $data['filename'];
            $this->isTestMode = $data['isTestMode'];
            $this->createdAt  = $data['createdAt'];

            $this->rootStep->fromArray($data);
        }

        return $this;
    }

    public function getStateFile(): string
    {
        return $this->config->getTmpPath() . DIRECTORY_SEPARATOR . $this->getFeed()->getId() . '.state';
    }

    public function getCreatedAt(): int
    {
        return (int)$this->createdAt;
    }
}
