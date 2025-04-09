<?php

namespace Magecomp\Mobilelogin\Controller\Adminhtml\Index;

use Magecomp\Mobilelogin\Helper\Data;
use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;

class Update extends Action
{
    protected $_customerRepoInterface;
    protected $_customerFactory;
	protected $resultJsonFactory;
    public function __construct(Context $context,
                                Data $helper,
                                JsonFactory $resultJsonFactory,
                                \Magento\Customer\Api\CustomerRepositoryInterface $customerRepoInterface,
                                \Magento\Customer\Model\CustomerFactory $customerFactory
    )
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->_customerRepoInterface = $customerRepoInterface;
        $this->_customerFactory = $customerFactory;
        parent::__construct($context);
    }

    public function execute()
    {
        $result = $this->resultJsonFactory->create();

         $websiteId = $this->getRequest()->getParam('website');
        if(!$websiteId){
            $websiteId =  (int)$this->helper->getWebsiteId();
        }
        

         $customerCollectoin = $this->_customerFactory->create()->getCollection()
            ->addAttributeToSelect("*")
            ->addAttributeToFilter('website_id', $websiteId)
            ->load();     
        foreach ($customerCollectoin as $customer){
            if($customer->getPrimaryBillingAddress()) {
                if ($customer->getPrimaryBillingAddress()->getTelephone()) {
                    $telephone = $customer->getPrimaryBillingAddress()->getTelephone();
                    $customerObj = $this->_customerRepoInterface->getById($customer->getId());
                    $customerObj->setCustomAttribute('mobilenumber', $telephone);
                    $this->_customerRepoInterface->save($customerObj);
                }
                else{
                    $customerObj = $this->_customerRepoInterface->getById($customer->getId());
                    $customerObj->setCustomAttribute('mobilenumber', null);
                    $this->_customerRepoInterface->save($customerObj);
                }
            }
        }
        return $result->setData(count($customerCollectoin));
    }
    protected function _isAllowed()
    {
        return true;
    }
}
