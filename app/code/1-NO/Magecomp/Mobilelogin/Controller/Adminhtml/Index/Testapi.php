<?php

namespace Magecomp\Mobilelogin\Controller\Adminhtml\Index;

use Magento\Backend\App\Action;
use Magento\Backend\App\Action\Context;
use Magento\Framework\Controller\Result\JsonFactory;
use Magecomp\Mobilelogin\Helper\Data;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\State;

class Testapi extends Action
{
    private $helper;
    protected $resultJsonFactory;
     protected $state;

    public function __construct(Context $context, 
                                Data $helper, 
                                JsonFactory $resultJsonFactory,
                                RequestInterface $request,
                                State $state)
    {
        $this->resultJsonFactory = $resultJsonFactory;
        $this->helper = $helper;
        $this->request = $request;
        $this->state = $state;
        parent::__construct($context);
    }

    public function execute()
    {
        $websiteId = $this->getRequest()->getParam('website');
        if(!$websiteId){
            $websiteId =  (int)$this->helper->getWebsiteId();
        }

        $result = $this->resultJsonFactory->create();
        return $result->setData($this->helper->sendAdminTestAPI($websiteId));
    }

    protected function _isAllowed()
    {
        return true;
    }
}
