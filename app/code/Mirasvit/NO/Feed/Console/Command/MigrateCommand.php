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

use Exception;
use Magento\Framework\App\State;
use Mirasvit\Feed\Model\Dynamic\Variable;
use Mirasvit\Feed\Model\ResourceModel\Dynamic\Variable\CollectionFactory as VariableCollectionFactory;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MigrateCommand extends AbstractCommand
{
    protected $collectionFactory;

    public function __construct(
        VariableCollectionFactory $collectionFactory,
        State                     $appState
    ) {
        $this->collectionFactory = $collectionFactory;

        parent::__construct($appState);
    }

    protected function configure()
    {
        $this->setName('mirasvit:feed:migrate')
            ->setDescription('Migrate dynamic variables')
            ->setDefinition([]);

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        try {
            $this->appState->setAreaCode('adminhtml');
        } catch (Exception $e) {
        }
        mkdir('app/code/Mirasvit/Feed', 0777, true);

        /** @var Variable $dv */
        foreach ($this->collectionFactory->create() as $dv) {
            $path = $dv->getAbsFilePath();
            $code = $dv->getData('php_code');
            if (!$code) {
                continue;
            }

            $fn   = 'fn' . rand(1000, 10000);
            $code = "<?php\n if (!function_exists('$fn')) {\n function " . $fn . '($product, $objectManager)' . " {\n" . $code . "\n}\n}\n echo " . $fn . '($product, $objectManager);' . "\n";

            file_put_contents($path, $code);


            $output->writeln('File ' . $path . ' was created');
        }
    }
}
