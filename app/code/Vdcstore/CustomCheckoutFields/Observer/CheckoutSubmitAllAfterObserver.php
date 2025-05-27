<?php

namespace Vdcstore\CustomCheckoutFields\Observer;

use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Magento\Framework\App\Http\Context as HttpContext;
use Magento\Framework\App\Area;

class CheckoutSubmitAllAfterObserver implements ObserverInterface
{
    /**
     * @var HttpContext
     */
    protected $httpContext;

    /**
     * Constructor
     *
     * @param HttpContext $httpContext
     */
    public function __construct(HttpContext $httpContext)
    {
        $this->httpContext = $httpContext;
    }

    /**
     * Execute
     *
     * @param Observer $observer
     * @return $this
     */
    public function execute(Observer $observer)
    {
        // Check if the area code is frontend
        if ($this->httpContext->getValue('area_code') !== Area::AREA_FRONTEND) {
            return $this; // Exit if not frontend
        }

        $order = $observer->getEvent()->getOrder();
        $quote = $observer->getEvent()->getQuote();
        if (empty($order) || empty($quote)) {
            return $this;
        }

        $shippingAddress = $quote->getShippingAddress();
        $orderShippingAddress = $order->getShippingAddress();

        // Add custom fields to the order shipping address
        $orderShippingAddress->setCustomField($shippingAddress->getCustomField());
        // $orderShippingAddress->setCheckoutField($shippingAddress->getCheckoutField());
        // $orderShippingAddress->setCustomCheckout($shippingAddress->getCustomCheckout());
        // $orderShippingAddress->setCustomConfig($shippingAddress->getCustomConfig());
        // $orderShippingAddress->setCustomFieldConfig($shippingAddress->getCustomFieldConfig());
        // $orderShippingAddress->setCheckoutFieldConfig($shippingAddress->getCheckoutFieldConfig());
        // $orderShippingAddress->setStoreCustomField($shippingAddress->getStoreCustomField());
        // $orderShippingAddress->setStoreCheckoutField($shippingAddress->getStoreCheckoutField());

        // Save the order shipping address
        $orderShippingAddress->save();

        return $this;
    }
}
