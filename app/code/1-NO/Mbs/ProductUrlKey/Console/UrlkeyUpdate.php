<?php
namespace Mbs\ProductUrlKey\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputOption;
use Magento\Framework\App\Action\Context;

class UrlkeyUpdate extends Command
{
   public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mbs\ProductUrlKey\Helper\ProductUrlkeyUpdate $urlkeyHelper
    )
    {
        parent::__construct();
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_urlkeyHelper = $urlkeyHelper;
    }

   protected function configure()
   {
        $options = [
           new InputOption(
               'from', // the option name
               '-fr', // the shortcut
               InputOption::VALUE_REQUIRED, // the option mode
               'Product Id From' // the description
           ),
           new InputOption(
               'to', // the option name
               '-to', // the shortcut
               InputOption::VALUE_REQUIRED, // the option mode
               'Product Id To' // the description
           ),
       ];
       $this->setName('mbs:producturlkey');
       $this->setDescription('Product UrlKey Updation');
       $this->setDefinition($options);

       // php bin/magento mbs:producturlkey --from=1 --to=50

       parent::configure();
   }
   
   protected function execute(InputInterface $input, OutputInterface $output)
   {
        $from = $input->getOption('from');
        if (!isset($from)) {
            $output->writeln('Please provide from!');
            return 0;
        }

        $to = $input->getOption('to');
        if (!isset($to)) {
            $output->writeln('Please provide to!');
            return 0;
        }
        $this->_urlkeyHelper->updateUrl($from, $to, $output);
        //$output->writeln("Hello World");
   }
}