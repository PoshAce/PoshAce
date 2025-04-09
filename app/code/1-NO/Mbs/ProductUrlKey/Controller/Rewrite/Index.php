<?php
namespace Mbs\ProductUrlKey\Controller\Rewrite;

use Magento\Framework\App\Action\Context;

class Index extends \Magento\Framework\App\Action\Action
{
    protected $_objectManager;
    protected $_storeManager;
    protected $_resource;
    protected $_moduleHelper;

    public function __construct(
        Context $context,
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
        $this->_url = $context->getUrl();

        parent::__construct($context);
    }

    public function execute()
    {
        if($this->_moduleHelper->chkIsModuleEnable()){

            $from = (int) $this->getRequest()->getParam('from');
            $to = (int) $this->getRequest()->getParam('to');

            $amount = ($to - $from) + 1;
            $from -= 1;
            $to += 1;

            $connection  = $this->_resource->getConnection();
            $tablePrefix = $this->_moduleHelper->getTablePrefix();

            $table = $connection->getTableName($tablePrefix.'catalog_product_entity');

            $query = "SELECT * FROM `".$table."` WHERE 1";
            $products = $connection->fetchAll($query);

            if(sizeof($products) > 0){
                foreach($products as $data){

                    $productId = (int) $data['entity_id'];
                    if($productId > $from && $productId < $to){
                        echo 'product #'.$productId.'<br>';
                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                        $product->setStoreId(0);
                        
                        try{
                            $urlKey = $this->_moduleHelper->getFormattedUrl($product->getSku(), $product->getName());
                            $product->setData('url_key', $urlKey);
                            $product->save();
                        }catch(\Exception $e){
                            echo $e->getMessage(); exit();
                        }
                    }
                }
            }

            $from = $to;
            $to = ($to + $amount) -1;

            echo '<a href="'.$this->_url->getUrl('producturlkey/rewrite/index', array('from' => $from, 'to' => $to)).'"> next import</a>';
            echo '<br><br>Successfully updated Product Url Key !!';exit();
        }else{
            echo 'Module is not enabled!!';exit();
        }
        
    }

}
