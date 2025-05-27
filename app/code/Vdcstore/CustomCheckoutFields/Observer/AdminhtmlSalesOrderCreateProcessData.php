<?php
namespace Vdcstore\CustomCheckoutFields\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;

class AdminhtmlSalesOrderCreateProcessData implements ObserverInterface
{
    /**
     * Execute
     *
     * @param EventObserver $observer
     * @return $this
     */
    public function execute(EventObserver $observer)
    {
        $requestData = $observer->getRequest();
        $customField = isset($requestData['custom_field']) ? $requestData['custom_field'] : null;
        $chekoutField = isset($requestData['checkout_field']) ? $requestData['checkout_field'] : null;
        $customCheckout = isset($requestData['custom_checkout']) ? $requestData['custom_checkout'] : null;
        $customConfig = isset($requestData['custom_config']) ? $requestData['custom_config'] : null;
        $customFieldConfig = isset($requestData['custom_field_config']) ? $requestData['custom_field_config'] : null;
        $checkoutFieldConfig = isset($requestData['checkout_field_config']) ?
            $requestData['checkout_field_config'] : null;
        $storeCustomField = isset($requestData['store_custom_field']) ? $requestData['store_custom_field'] : null;
        $storeCheckoutField = isset($requestData['store_checkout_field']) ? $requestData['store_checkout_field'] : null;

        /** @var \Magento\Sales\Model\AdminOrder\Create $orderCreateModel */
        $orderCreateModel = $observer->getOrderCreateModel();
        $quote = $orderCreateModel->getQuote();
        $quote->setCustomField($customField);
        $quote->setCheckoutField($chekoutField);
        $quote->setCustomCheckout($customCheckout);
        $quote->setCustomConfig($customConfig);
        $quote->setCustomFieldConfig($customFieldConfig);
        $quote->setCheckoutFieldConfig($checkoutFieldConfig);
        $quote->setStoreCustomField($storeCustomField);
        $quote->setStoreCheckoutField($storeCheckoutField);

        return $this;
    }
}
