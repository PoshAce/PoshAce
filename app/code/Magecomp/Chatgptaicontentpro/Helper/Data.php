<?php
namespace Magecomp\Chatgptaicontentpro\Helper;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\App\Helper\AbstractHelper;

class Data extends AbstractHelper
{
   const XML_IS_ENABLE = 'chatgptaicontentpro/general/enabled';
   const XML_SHORT_DIS_LENGTH = 'chatgptaicontentpro/api/shortdis';
   const XML_DIS_LENGTH = 'chatgptaicontentpro/api/dis';
   const XML_DIS_PROMPT = 'chatgptaicontentpro/api/dispromt';
   const XML_SHORT_DIS_PROMPT = 'chatgptaicontentpro/api/shortdispromt';
   const XML_SHORT_LANG = 'chatgptaicontentpro/api/chatgptlanguage';
   const XML_CHAT_MODEL = 'chatgptaicontentpro/api/model';
   const XML_PATH_TOKEN = 'chatgptaicontentpro/api/token';
   const XML_PATH_BASE_URL = 'chatgptaicontentpro/api/base_url';
   protected $scopeConfig;
   protected $storeManager;

   public function __construct(
        ScopeConfigInterface $scopeConfig,\Magento\Store\Model\StoreManagerInterface $storeManager
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->storeManager = $storeManager;
    }

    public function getBaseurl($storeid=null)
    {
        $getBaseurl = $this->scopeConfig->getValue(self::XML_PATH_BASE_URL,ScopeInterface::SCOPE_STORE,$storeid);
        return $getBaseurl;
    }

       public function isEnabled($storeid=null)
    {
        $enabled = $this->scopeConfig->getValue(self::XML_IS_ENABLE,ScopeInterface::SCOPE_STORE,$storeid);
        return $enabled;
    }
    public function getApikey($storeid=null)
    {
        $getApikey = $this->scopeConfig->getValue(self::XML_PATH_TOKEN,ScopeInterface::SCOPE_STORE,$storeid);
        return $getApikey;
    }
   
   public function getShortDisLen($storeid=null)
    {
        $sortDisValue = $this->scopeConfig->getValue(self::XML_SHORT_DIS_LENGTH,ScopeInterface::SCOPE_STORE,$storeid);
        return $sortDisValue;
    }
    public function getDisLen($storeid=null)
    {
        $disValue = $this->scopeConfig->getValue(self::XML_DIS_LENGTH,ScopeInterface::SCOPE_STORE,$storeid);
        return $disValue;
    }
     public function getDisPromt($storeid=null)
    {
        $disPromot = $this->scopeConfig->getValue(self::XML_DIS_PROMPT,ScopeInterface::SCOPE_STORE,$storeid);
        return $disPromot;
    }
     public function getShortDisPromt($storeid=null)
    {
        $shortDisPromot = $this->scopeConfig->getValue(self::XML_SHORT_DIS_PROMPT,ScopeInterface::SCOPE_STORE,$storeid);
        return $shortDisPromot;
    }
     public function getChatgptModel($storeid=null)
    {
        $chatgptModel = $this->scopeConfig->getValue(self::XML_CHAT_MODEL,ScopeInterface::SCOPE_STORE,$storeid);
        return $chatgptModel;
    }
      public function getLanguage($storeid=null)
    {
       if ($storeid === null) {
        $storeid = $this->storeManager->getStore()->getId();
    }

        $language = $this->scopeConfig->getValue(self::XML_SHORT_LANG,ScopeInterface::SCOPE_STORE,$storeid);
        return $language;
    }
}