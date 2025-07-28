<?php
namespace Ithinklogistics\Ithinklogistics\Observer;
 
use Magento\Framework\Event\ObserverInterface;
use Psr\Log\LoggerInterface;
 
class Orderplaceafter implements ObserverInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        try 
        {
            $order = $observer->getEvent()->getOrder();
            var_dump($order);
            $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new  \Laminas\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($order);
        } 
        catch (\Exception $e) 
        {
            $error_message = $this->logger->info($e->getMessage());
            $writer = new \Laminas\Log\Writer\Stream(BP . '/var/log/test.log');
            $logger = new  \Laminas\Log\Logger();
            $logger->addWriter($writer);
            $logger->info($error_message);
        }
    }
}
?>