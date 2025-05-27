<?php
namespace Vdcstore\CustomCheckoutFields\Observer;

use Magento\Framework\Event\Observer as EventObserver;
use Magento\Framework\Event\ObserverInterface;
use Magento\Quote\Model\QuoteRepository;

class SalesModelServiceQuoteSubmitBefore implements ObserverInterface
{
    /**
     * @var QuoteRepository
     */
    private $quoteRepository;

    /**
     * SalesModelServiceQuoteSubmitBefore constructor.
     *
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * Execute
     *
     * @param EventObserver $observer
     * @return $this
     * @throws \Exception
     */
    public function execute(EventObserver $observer)
    {
        $order = $observer->getOrder();
        /** @var \Magento\Quote\Model\Quote $quote */
        $quote = $this->quoteRepository->get($order->getQuoteId());

        // Set additional fields from quote to order
        $order->setCustomField($quote->getCustomField());
        // $order->setCheckoutField($quote->getCheckoutField());
        // $order->setCustomCheckout($quote->getCustomCheckout());
        // $order->setCustomConfig($quote->getCustomConfig());
        // $order->setCustomFieldConfig($quote->getCustomFieldConfig());
        // $order->setCheckoutFieldConfig($quote->getCheckoutFieldConfig());
        // $order->setStoreCustomField($quote->getStoreCustomField());
        // $order->setStoreCheckoutField($quote->getStoreCheckoutField());

        return $this;
    }
}
