<?php
namespace Magecomp\Mobilelogin\Plugin;

use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Framework\Controller\ResultFactory;
use Magento\Framework\Message\ManagerInterface;
use Magento\Framework\App\RequestInterface;

class Checkattribute
{
    protected $_helperData;
    protected $request;

    public function __construct(
        ResultFactory $Redirect,
        ManagerInterface $messageManager,
        MagecompHelper   $helperData,
        RequestInterface $request
    ) {
        $this->resultFactory = $Redirect;
        $this->_helperData = $helperData;
        $this->_messageManager = $messageManager;
        $this->getRequest = $request;
    }

    public function aroundexecute(
        \Magento\Customer\Controller\Adminhtml\Index\Save $subject,
        $proceed,
        $data = "null",
        $requestInfo = false
    ) {
        $resultRedirect = $this->resultFactory->create(ResultFactory::TYPE_REDIRECT);
        $data = $subject->getRequest()->getPostValue();
        if (isset($data['customer']) || $data['customer'] != '') {
                $customerCollection = $this->_helperData->checkCustomerExists($data['customer']['mobilenumber'], "mobile", 1);
            if (count($customerCollection) > 0) {
                foreach ($customerCollection as $customer) {
                    if ($customer->getId()!=$data['customer']['entity_id']) {
                        $this->_messageManager->addError(__('Mobile No Has Already Exist'));
                        return $resultRedirect->setRefererOrBaseUrl();
                    }
                }
            }
        }

        return $proceed();
    }
}
