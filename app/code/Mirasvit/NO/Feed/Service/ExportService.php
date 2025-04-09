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

namespace Mirasvit\Feed\Service;

use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;
use Mirasvit\Core\Service\YamlService;
use Mirasvit\Core\Helper\Io;
use Mirasvit\Feed\Model\Config;

class ExportService
{
    protected $yaml;

    protected $config;

    protected $messageManager;

    protected $io;

    public function __construct(
        YamlService             $yaml,
        Config                  $config,
        MessageManagerInterface $messageManager,
        Io                      $io
    ) {
        $this->yaml           = $yaml;
        $this->config         = $config;
        $this->messageManager = $messageManager;
        $this->io             = $io;
    }

    public function export(object $entityModel, string $relativePath): bool
    {
        $absPath = $this->config->absolutePath($relativePath);

        $yaml = $this->yaml->dump(
            $entityModel->toArray($entityModel->getRowsToExport()),
            10
        );

        if ($this->io->isWritable($this->io->getParentDirectory($absPath))) {
            $this->io->filePutContents($absPath, $yaml);

            return true;
        } else {
            $this->messageManager->addWarningMessage(
                __(
                    'There is no permission to export files. Please set Write access to "%1"',
                    $this->config->printPath($relativePath)
                )
            );
        }

        return false;
    }
}
