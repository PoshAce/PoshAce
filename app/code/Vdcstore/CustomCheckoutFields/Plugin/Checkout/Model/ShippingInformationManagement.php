<?php
namespace Vdcstore\CustomCheckoutFields\Plugin\Checkout\Model;

use Magento\Quote\Model\QuoteRepository;
use Magento\Checkout\Api\Data\ShippingInformationInterface;

class ShippingInformationManagement
{
    /**
     * @var QuoteRepository
     */
    protected $quoteRepository;

    /**
     * ShippingInformationManagement constructor.
     *
     * @param QuoteRepository $quoteRepository
     */
    public function __construct(
        QuoteRepository $quoteRepository
    ) {
        $this->quoteRepository = $quoteRepository;
    }

    /**
     * BeforeSaveAddressInformation
     *
     * @param \Magento\Checkout\Model\ShippingInformationManagement $subject
     * @param int $cartId
     * @param ShippingInformationInterface $addressInformation
     */
    public function beforeSaveAddressInformation(
        \Magento\Checkout\Model\ShippingInformationManagement $subject,
        $cartId,
        ShippingInformationInterface $addressInformation
    ) {
        $extAttributes = $addressInformation->getExtensionAttributes();
        $quote = $this->quoteRepository->getActive($cartId);
        // Set additional fields from extension attributes to quote
        $quote->setCustomField($extAttributes->getCustomField());
        // $quote->setCheckoutField($extAttributes->getCheckoutField());
        // $quote->setCustomCheckout($extAttributes->getCustomCheckout());
        // $quote->setCustomConfig($extAttributes->getCustomConfig());
        // $quote->setCustomFieldConfig($extAttributes->getCustomFieldConfig());
        // $quote->setCheckoutFieldConfig($extAttributes->getCheckoutFieldConfig());
        // $quote->setStoreCustomField($extAttributes->getStoreCustomField());
        // $quote->setStoreCheckoutField($extAttributes->getStoreCheckoutField());
    }
}
