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
use Mirasvit\Core\Helper\Io;
use Mirasvit\Feed\Model\Config;

class Initialization extends AbstractStep
{

    protected $io;

    protected $config;

    public function __construct(
        Config  $config,
        Io      $io,
        Context $context
    ) {
        $this->io     = $io;
        $this->config = $config;

        parent::__construct($context);
    }

    public function execute()
    {
        $tmpPath = $this->config->getTmpPath();

        $this->io->unlink($this->context->getStateFile());
        $this->io->unlink($tmpPath . DIRECTORY_SEPARATOR . $this->context->getFeed()->getId() . '.dat');

        $this->index = 1;
    }
}
