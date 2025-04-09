<?php

namespace Magecomp\Pagenotfound\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
class Data extends AbstractHelper
{
    protected $_storeManager;
    const PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_ENABLE = 'pagenotfound_configuration/pagenotfoundoption/enable';
    const PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_RECIPIENT_EMAIL = 'pagenotfound_configuration/pagenotfoundoption/recipient_email';
    const PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_SENDER_EMAIL_IDENTTITY = 'pagenotfound_configuration/pagenotfoundoption/sender_email_identity';
    const PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_PAGENOTFOUNDOPTION_EMAIL_TEMPLATE = 'pagenotfound_configuration/pagenotfoundoption/email_template';
    const PAGENOTFOUND_CONFIGRATION_CONFIGRATION_CMSPAGEREDIRECT = 'pagenotfound_configuration/configuration/cmspageredirect';
    const PAGENOTFOUND_CONFIGRATION_CONFIGRATION_CMS_PAGE_ROUTE_CHOOSE = 'pagenotfound_configuration/configuration/cmspageroutechoose';

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        array $data = []
    )
    {
        $this->_storeManager = $storeManager;
        parent::__construct($context);
    }

    public function isEnabled()
    {
        $store = $this->_storeManager->getStore();
        $configValue = $this->scopeConfig->getValue(
            self::PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_ENABLE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue;
    }

    public function Recipientemail()
    {
        $store = $this->_storeManager->getStore();
        $configValue = $this->scopeConfig->getValue(
            self::PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_RECIPIENT_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue;
    }

    public function Emailsenderidentity()
    {
        $store = $this->_storeManager->getStore();
        $configValue = $this->scopeConfig->getValue(
            self::PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_SENDER_EMAIL_IDENTTITY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue;
    }

    public function Pagenotfoundoption()
    {
        $store = $this->_storeManager->getStore();
        $configValue = $this->scopeConfig->getValue(
            self::PAGENOTFOUND_CONFIGRATION_PAGENOTFOUNDOPTION_PAGENOTFOUNDOPTION_EMAIL_TEMPLATE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue;
    }

    public function getcmspage()
    {
        $store = $this->_storeManager->getStore();
        $configValue = $this->scopeConfig->getValue(
            self::PAGENOTFOUND_CONFIGRATION_CONFIGRATION_CMSPAGEREDIRECT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue;
    }

    public function getCmspageroutechoose()
    {
        $store = $this->_storeManager->getStore();
        $configValue = $this->scopeConfig->getValue(
            self::PAGENOTFOUND_CONFIGRATION_CONFIGRATION_CMS_PAGE_ROUTE_CHOOSE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
        return $configValue;
    }
}
