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
use Mirasvit\Feed\Model\Config;

class ImportService
{
    protected $yaml;

    protected $config;

    protected $messageManager;

    public function __construct(
        YamlService             $yaml,
        Config                  $config,
        MessageManagerInterface $messageManager
    ) {
        $this->yaml           = $yaml;
        $this->config         = $config;
        $this->messageManager = $messageManager;
    }

    public function import(object $object, string $filePath)
    {
        $filePath = $this->config->absolutePath($filePath);

        if (is_readable($filePath)) {
            $content = file_get_contents($filePath);
            $data    = $this->yaml->parse($content);

            $object->setData($data);
            $object->setIsActive(true);
            $object->save();

            return $object;
        } else {
            $this->messageManager->addWarningMessage(
                __(
                    'There is no permission to import files. Please set Read access to the folder "%1" to import templates',
                    $this->config->printPath($this->config->getTemplatePath())
                )
            );
        }
    }
}
