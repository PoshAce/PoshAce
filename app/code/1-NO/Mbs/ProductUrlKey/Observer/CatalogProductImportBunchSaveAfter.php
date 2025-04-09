<?php

namespace Mbs\ProductUrlKey\Observer;

use Magento\Framework\Event\ObserverInterface;

class CatalogProductImportBunchSaveAfter implements ObserverInterface
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

    public function execute(\Magento\Framework\Event\Observer $observer)
    {
        if($this->_moduleHelper->chkIsModuleEnable()){
            //$eventName = $observer->getEvent()->getName();
                
            $productImport = $observer->getEvent()->getData('bunch');
            foreach($productImport as $importId){

                $productId = $this->_objectManager->get('Magento\Catalog\Model\Product')->getIdBySku($importId['sku']);
                if($productId){
                    $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                    $product->setStoreId(0);
                    
                    try{
                        $urlKey= $this->_moduleHelper->getFormattedUrl($product->getSku(), $product->getName());
                        $product->setData('url_key', $urlKey);
                        $product->save();
                    }catch(\Exception $e){
                        echo $e->getMessage(); exit();
                    }
                }
            }
        }

        return $this;    
    }

}