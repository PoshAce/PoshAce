<?php
namespace Magecomp\Chatgptaicontentpro\Block\Adminhtml\Product;

use Magento\Catalog\Model\Locator\LocatorInterface;
use Magento\Framework\View\Element\Template;
use Magento\Store\Api\StoreRepositoryInterface;
use Magecomp\Chatgptaicontentpro\Model\Config;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\UrlInterface;

class Helper extends Template
{
    private Config $config;
    private StoreRepositoryInterface $storeRepository;
    private LocatorInterface $locator;
    private Json $json;
    protected $urlInterface;

    public function __construct(
        Template\Context $context,
        Config $config,
        StoreRepositoryInterface $storeRepository,
        LocatorInterface $locator,
        Json $json,
         UrlInterface $urlInterface,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->config = $config;
        $this->storeRepository = $storeRepository;
        $this->locator = $locator;
        $this->json = $json;
        $this->urlInterface = $urlInterface;
    }

    public function getComponentJsonConfig(): string
    {
        $config = [
            'serviceUrl' => $this->getUrl('Magecomp_Chatgptaicontentpro/helper/validate'),
            'sku' => $this->locator->getProduct()->getSku(),
            'storeId' => $this->locator->getStore()->getId(),
            'stores' => $this->getStores()
        ];
        return $this->json->serialize($config);
    }

    public function getStores(): array
    {
        $selectedStoreId = (int) $this->locator->getStore()->getId();
        $storeIds = $this->config->getEnabledStoreIds();

        $results = [];
        $first = null;
        foreach ($storeIds as $storeId) {
            $store = $this->storeRepository->getById($storeId);
            if ($selectedStoreId === $storeId) {
                $first = $store;
                continue;
            }
            $results[] = [
                'label' => $storeId === 0 ? __('Default scope') : $store->getName(),
                'store_id' => $storeId,
                'selected' => false
            ];
        }

        if ($first) {
            array_unshift($results, [
                'label' => __('Current scope'),
                'store_id' => $first->getId(),
                'selected' => true
            ]);
        }

        return $results;
    }

    public function toHtml(): string
    {
        return parent::toHtml();
    }
    public function getCurrentUrl()
{
    $currentUrl = $this->urlInterface->getCurrentUrl();
    
    // Look for the position of '/store/' in the URL
    $storePos = strpos($currentUrl, '/store/');
    
    if ($storePos !== false) {
        // Extract the substring after '/store/'
        $storeSubstr = substr($currentUrl, $storePos + 7); // 7 because '/store/' is 7 characters long
        
        // Split the string by '/' to get the parts
        $storeParts = explode('/', $storeSubstr);
        
        // The store ID should be the first part after '/store/'
        $storeId = $storeParts[0];
        
        return $storeId;
    } else {
        return 0; // Default to 0 if '/store/' is not found
    }
}
}
