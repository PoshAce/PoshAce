<?php

namespace Magecomp\Chatgptaicontentpro\Model;

use Magento\Framework\App\Config\ScopeConfigInterface;

class Config
{
    public const XML_PATH_ENABLED = 'chatgptaicontentpro/general/enabled';
    public const XML_PATH_BASE_URL = 'chatgptaicontentpro/api/base_url';
    public const XML_PATH_TOKEN = 'chatgptaicontentpro/api/token';
    public const XML_PATH_STORES = 'chatgptaicontentpro/general/stores';
    public const XML_PATH_MODEL = 'chatgptaicontentpro/api/model';
    public const XML_PATH_LANGUAGE = 'chatgptaicontentpro/api/chatgptlanguage';
    public const XML_PATH_SHORTLEN = 'chatgptaicontentpro/api/shortdis';
    public const XML_PATH_DISLEN = 'chatgptaicontentpro/api/dis';
    public const XML_PATH_DISPROMT = 'chatgptaicontentpro/api/dispromt';
    public const XML_PATH_SHORTPROMT = 'chatgptaicontentpro/api/shortdispromt';
    private ScopeConfigInterface $scopeConfig;

    public function __construct(ScopeConfigInterface $scopeConfig)
    {
        $this->scopeConfig = $scopeConfig;
    }

    /**
     * @return mixed
     */
    public function getValue(string $path, $scopeType = ScopeConfigInterface::SCOPE_TYPE_DEFAULT, $scopeCode = null)
    {
        return $this->scopeConfig->getValue($path, $scopeType, $scopeCode);
    }

    /**
     * @return int[]
     */
    public function getEnabledStoreIds(): array
    {
        $stores = $this->scopeConfig->getValue(self::XML_PATH_STORES);

        if ($stores === null || $stores === '') {
            $storeList = [0];
        } else {
            $storeList = $stores === '0' ? [0] : array_map('intval', explode(',', $stores));
        }
        sort($storeList, SORT_NUMERIC);

        return $storeList;
    }
}
