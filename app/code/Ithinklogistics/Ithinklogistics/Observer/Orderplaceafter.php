<?php
namespace Ithinklogistics\Ithinklogistics\Observer;

use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\Event\Observer;
use Psr\Log\LoggerInterface;

class Orderplaceafter implements ObserverInterface
{
    protected $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function execute(Observer $observer)
    {
        try {
            $order = $observer->getEvent()->getOrder();

            // Log specific order data to var/log/your_custom_log.log
            $logData = [
                'Order ID' => $order->getIncrementId(),
                'Customer Email' => $order->getCustomerEmail(),
                'Grand Total' => $order->getGrandTotal(),
                'Shipping Method' => $order->getShippingMethod()
            ];

            $this->logger->info('Order Placed Data: ' . json_encode($logData));
        } catch (\Exception $e) {
            $this->logger->error('Error in Orderplaceafter Observer: ' . $e->getMessage());
        }
    }
}
