<?php

namespace Magetrend\PdfTemplates\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;

class Vars implements ObserverInterface
{
    public function execute(Observer $observer)
    {
        $order = $observer->getSource();
        if (!$order instanceof \Magento\Sales\Model\Order) {
            $order = $order->getOrder();
        }

        // Fetch custom_field value from the order
        $customField = $order->getData('custom_field');

        // Set it in the variable list
        $observer->getVariableList()->setData('gst_number', $customField);
    }
}
