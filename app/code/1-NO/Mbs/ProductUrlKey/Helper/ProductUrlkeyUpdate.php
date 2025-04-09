<?php
namespace Mbs\ProductUrlKey\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;
use Magento\Framework\App\Action\Context;

class ProductUrlkeyUpdate extends AbstractHelper {

	protected $_objectManager;
    protected $_storeManager;
    protected $_resource;
    protected $_moduleHelper;
	
	public function __construct(
        Context $context,
        \Magento\Framework\App\State $state,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\App\ResourceConnection $resource,
        \Mbs\ProductUrlKey\Helper\Data $moduleHelper
	)
    {
        $this->state = $state;
		$this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->_resource = $resource;
        $this->_moduleHelper = $moduleHelper;
        $this->_url = $context->getUrl();
    }

    public function updateUrl($from, $to, $output)
    {
        if($this->_moduleHelper->chkIsModuleEnable()){
            $this->state->setAreaCode(\Magento\Framework\App\Area::AREA_ADMINHTML); 
            $from = (int) $from;
            $to = (int) $to;

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

                        $product = $this->_objectManager->create('Magento\Catalog\Model\Product')->load($productId);
                        $product->setStoreId(0);
                        
                        try{
                            $urlKey = $this->_moduleHelper->getFormattedUrl($product->getSku(), $product->getName());
                            $product->setData('url_key', $urlKey);
                            $product->save();

                            $output->writeln('product #'.$productId.' updated.');
                        }catch(\Exception $e){
                            echo $e->getMessage(); exit();
                        }
                    }
                }
            }

            $from = $to;
            $to = ($to + $amount) -1;

            $output->writeln('<a href="'.$this->_url->getUrl('producturlkey/rewrite/index', array('from' => $from, 'to' => $to)).'"> next import</a>');
            $output->writeln('Successfully updated Product Url Key !!');
        }else{
            $output->writeln('Module is not enabled!!');
        }


    }

}
