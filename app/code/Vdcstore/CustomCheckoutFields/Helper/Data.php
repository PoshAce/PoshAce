<?php
namespace Vdcstore\CustomCheckoutFields\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper
{
    public const XML_PATH_ECOMMERCE = 'custom_checkout/';

    /**
     * GetConfigValue
     *
     * @param string $field
     * @param int $storeCode
     * @return mixed
     */
    public function getConfigValue($field, $storeCode = null)
    {
           return $this->scopeConfig->getValue($field, ScopeInterface::SCOPE_STORE, $storeCode);
    }
    /**
     * GetConfigValue
     *
     * @param string $fieldid
     * @param int $storeCode
     * @return mixed
     */
    public function getGeneralConfig($fieldid, $storeCode = null)
    {
           return $this->getConfigValue(self::XML_PATH_ECOMMERCE.'general/'.$fieldid, $storeCode);
    }
    /**
     * GetConfigValue
     *
     * @param string $fieldid
     * @param int $storeCode
     * @return mixed
     */
    public function getCustomFieldValue($fieldid, $storeCode = null)
    {
           return $this->getConfigValue(self::XML_PATH_ECOMMERCE.'add_custom_field_1/'.$fieldid, $storeCode);
    }
    /**
     * GetConfigValue
     *
     * @param string $fieldid
     * @param int $storeCode
     * @return mixed
     */
    

       /**
        * Get visibility for the custom field
        *
        * @param int|null $storeCode
        * @return mixed
        */
    public function isCustomFieldVisible1($storeCode = null)
    {
        return $this->getConfigValue(self::XML_PATH_ECOMMERCE . 'add_custom_field_1/show_custom_field_1', $storeCode);
    }
    
}
