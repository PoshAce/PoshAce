<?php
namespace Mbs\ProductUrlKey\Helper;

use Magento\Framework\App\Helper\AbstractHelper;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Store\Model\ScopeInterface;

class Data extends AbstractHelper {

	const Mbs_ProductUrlKey_XML_PATH_EXTENSIONS = 'producturlkey/general/';

	protected $scopeConfig;
	protected $_objectManager;
    protected $_storeManager;
     protected $filter;
    protected $_resource;
	
	public function __construct(
		\Magento\Framework\View\Element\Context $context,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Filter\FilterManager $filter,
        \Magento\Framework\App\ResourceConnection $resource
	)
    {
        $this->scopeConfig = $context->getScopeConfig();
		$this->_objectManager = $objectManager;
        $this->_storeManager = $storeManager;
        $this->filter = $filter;
        $this->_resource = $resource;
    }

    public function getModuleConfig($path, $storeId = null)
    {
        return $this->scopeConfig->getValue(
            $path,
            ScopeInterface::SCOPE_STORE,
            $storeId
        );
    }

    public function chkIsModuleEnable(){

        return 1;
    	//return $this->getModuleConfig(self::Mbs_ProductUrlKey_XML_PATH_EXTENSIONS . 'isenabled');
    }

    public function getTablePrefix(){

        $deploymentConfig = $this->_objectManager->get('Magento\Framework\App\DeploymentConfig');
        return $deploymentConfig->get('db/table_prefix');
    }

    public function getFormattedUrl($sku, $name){

        $urlKey = strtolower($sku . '-'. $name);
        $urlKey = $this->filter->translitUrl($urlKey);
        return $urlKey;
    }

}
