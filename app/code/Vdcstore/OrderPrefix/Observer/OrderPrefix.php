<?php

namespace Vdcstore\OrderPrefix\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Sales\Model\Order;

class OrderPrefix implements ObserverInterface
{
    /**
     * Execute method to add prefix to order increment ID if enabled.
     *
     * @param Observer $observer
     */
    public function execute(Observer $observer)
    {
        $orderInstance = $observer->getEvent()->getOrder();

        $prefix = '1'; 

        if ($prefix) {
            $incrementId = $orderInstance->getIncrementId();

            // Avoid duplicate prefixes
            if (strpos($incrementId, $prefix) !== 0) {
                $newIncrementId = $prefix . $incrementId;
                $orderInstance->setIncrementId($newIncrementId);
                $orderInstance->save();
            }
        }
    }
}
