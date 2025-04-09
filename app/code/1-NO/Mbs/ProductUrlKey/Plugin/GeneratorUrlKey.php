<?php

namespace Mbs\ProductUrlKey\Plugin;

class GeneratorUrlKey
{
    protected $_objectManager;
    protected $_storeManager;
    protected $_resource;
    protected $_moduleHelper;

    public function __construct(
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mbs\ProductUrlKey\Helper\Data $moduleHelper
    )
    {
        $this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_moduleHelper = $moduleHelper;
    }

    public function aroundGetUrlKey(
        \Magento\CatalogUrlRewrite\Model\ProductUrlPathGenerator $subject,
        callable $proceed,
        \Magento\Catalog\Model\Product $product
    ) {
        
        if ($this->_moduleHelper->chkIsModuleEnable()) {
            $originalProductName = $product->getName();
            $sku = $product->getSku();

            $product->setName($sku . '-'. $originalProductName);
            $product->setData('url_key', null);
            $result = $proceed($product);
            $product->setName($originalProductName);
            
        } else {
            $result = $proceed($product);
        }

        return $result;
    }

    private function getBrandProductName(\Magento\Catalog\Model\Product $product)
    {
        return $product->getAttributeText('brand');
    }

}