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

namespace Mirasvit\Feed\Model;

use Magento\Framework\App\Filesystem\DirectoryList;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Filesystem;
use Mirasvit\Core\Helper\Io;

class Config
{
    const STATUS_COMPLETED  = 'completed';
    const STATUS_READY      = 'ready';
    const STATUS_PROCESSING = 'processing';
    const STATUS_ERROR      = 'error';

    protected $filesystem;

    protected $io;

    private   $scopeConfig;

    public function __construct(
        ScopeConfigInterface $scopeConfig,
        Filesystem           $filesystem,
        Io                   $io
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->filesystem  = $filesystem;
        $this->io          = $io;
    }

    public function isReportsEnabled(): bool
    {
        return (bool)$this->scopeConfig->getValue('feed/general/reports_enabled');
    }

    public function clearBasePath(): bool
    {
        $this->io->rmdirRecursive($this->getBasePath());

        return true;
    }

    public function getRootPath(): string
    {
        return $this->filesystem->getDirectoryRead(DirectoryList::ROOT)->getAbsolutePath();
    }

    public function getBasePath(): string
    {
        if ($this->io->isRemoteStorageEnabled()) {
            $path = DirectoryList::MEDIA . DIRECTORY_SEPARATOR . 'feed';
        } else {
            $path = $this->filesystem->getDirectoryRead(DirectoryList::MEDIA)->getAbsolutePath() . 'feed';
        }

        if (!$this->io->dirExists($path)) {
            $this->io->mkdir($path);
        }

        return $path;
    }

    public function getTmpPath(): string
    {
        $path = $this->getBasePath() . DIRECTORY_SEPARATOR . 'tmp';

        if (!$this->io->dirExists($path)) {
            $this->io->mkdir($path);
        }

        return $path;
    }

    public function getTemplatePath(): string
    {
        $rel = 'Setup/data/template';
        $abs = dirname(dirname(__FILE__)) . DIRECTORY_SEPARATOR . $rel;

        if (!$this->io->dirExists($abs)) {
            $this->io->mkdir($abs);
        }

        return $rel;
    }

    public function getRulePath(): string
    {
        $rel = 'rule';
        $abs = $this->getBasePath() . DIRECTORY_SEPARATOR . $rel;

        if (!$this->io->dirExists($abs)) {
            $this->io->mkdir($abs);
        }

        return $rel;
    }

    public function getDynamicAttributePath(): string
    {
        $rel = 'dynamic/attribute';
        $abs = $this->getBasePath() . DIRECTORY_SEPARATOR . $rel;

        if (!$this->io->dirExists($abs)) {
            $this->io->mkdir($abs);
        }

        return $rel;
    }

    public function getDynamicCategoryPath(): string
    {
        $rel = 'dynamic/category';
        $abs = $this->getBasePath() . DIRECTORY_SEPARATOR . $rel;

        if (!$this->io->dirExists($abs)) {
            $this->io->mkdir($abs);
        }

        return $rel;
    }

    public function getDynamicVariablePath(): string
    {
        $rel = 'dynamic/variable';
        $abs = $this->getBasePath() . DIRECTORY_SEPARATOR . $rel;

        if (!$this->io->dirExists($abs)) {
            $this->io->mkdir($abs);
        }

        return $rel;
    }

    public function getMaxAllowedTime(): int
    {
        $time = (int)ini_get('max_execution_time');

        if ($time < 1 || $time > 30) {
            $time = 20;
        }

        return $time;
    }

    public function getMaxAllowedMemory(): int
    {
        return 220 * 1024 * 1024;
    }

    public function printPath(string $path): string
    {
        $rootPath = $this->getRootPath();

        return str_replace($rootPath, '[Magento root dir]/', $this->absolutePath($path));
    }

    public function relativePath(string $path): string
    {
        $rootPath = $this->getRootPath();

        return str_replace($rootPath, '', $path);
    }

    public function absolutePath(string $path): string
    {
        if (substr($path, 0, 5) === 'Setup') {
            return dirname(dirname(__FILE__)) . '/' . $path;
        }

        return $this->getBasePath() . '/' . $path;
    }

    public function getFacebookAppID(): string
    {
        return "359775291691249";
    }
}
