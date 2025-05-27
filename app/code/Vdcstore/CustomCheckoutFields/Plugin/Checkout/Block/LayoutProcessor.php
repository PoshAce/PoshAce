<?php
namespace Vdcstore\CustomCheckoutFields\Plugin\Checkout\Block;

use Vdcstore\CustomCheckoutFields\Model\Config;
use Vdcstore\CustomCheckoutFields\Helper\Data;

class LayoutProcessor
{
    /**
     * @var Config
     */
    protected $config;

    /**
     * @var Data
     */
    protected $dataHelper;

    /**
     * LayoutProcessor constructor.
     *
     * @param Config $config
     * @param Data $helper
     */
    public function __construct(
        Config $config,
        Data $helper
    ) {
        $this->config = $config;
        $this->dataHelper = $helper;
    }

    /**
     * Modify jsLayout for custom checkout fields.
     *
     * @param \Magento\Checkout\Block\Checkout\LayoutProcessor $subject
     * @param array $jsLayout
     * @return array
     */
    public function afterProcess(
        \Magento\Checkout\Block\Checkout\LayoutProcessor $subject,
        array $jsLayout
    ) {
        $status = $this->config->getstatus();
        if ($status == 1) {

         

            
            // Add GST Number field
            $jsLayout['components']['checkout']['children']['steps']['children']
            ['shipping-step']['children']['shippingAddress']['children']
            ['shipping-address-fieldset']['children']['custom_field'] = [
                'component' => 'Magento_Ui/js/form/element/abstract',
                'config' => [
                    'customScope' => 'shippingAddress.custom_attributes',
                    'template' => 'ui/form/field',
                    'elementTmpl' => 'ui/form/element/input',
                    'id' => 'custom_field'
                ],
                'dataScope' => 'shippingAddress.custom_attributes.custom_field',
                'label' => __('GST Number'),
                'provider' => 'checkoutProvider',
                'visible' => true,
                'validation' => [
                    'required-entry' => true,
                    'max_text_length' => 15,
                    'min_text_length' => 15,
                    'validate-gst-number' => true
                ],
                'sortOrder' => 65,
                'id' => 'custom_field',
            ];

          
        }

        return $jsLayout;
    }
}
