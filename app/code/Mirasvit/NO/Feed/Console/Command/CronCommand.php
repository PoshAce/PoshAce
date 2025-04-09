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

namespace Mirasvit\Feed\Console\Command;

use Magento\Framework\App\State;
use Mirasvit\Feed\Cron\Export as CronExport;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CronCommand extends AbstractCommand
{
    protected $cronExport;

    public function __construct(CronExport $cronExport, State $appState)
    {
        $this->cronExport = $cronExport;
        parent::__construct($appState);
    }

    protected function configure()
    {
        $this->setName('mirasvit:feed:cron')
            ->setDescription('Run cron jobs for extension')
            ->setDefinition([]);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->appState->setAreaCode('frontend');

        $this->cronExport->execute();

        return 0;
    }
}
