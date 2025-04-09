<?php

namespace Magecomp\Mobilelogin\Plugin;

use Magecomp\Mobilelogin\Model\OtpFactory;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Framework\Controller\Result\RedirectFactory;
use Magento\Framework\UrlFactory;

class Pluginconfirmregistration
{
    protected $messageManager;
    protected $session;
    protected $_urlManager;
    protected $redirect;
    protected $_modelRegOtpFactory;
    protected $_responseFactory;
    protected $_request;
    protected $_helperData;
    protected $urlModel;
    protected $resultRedirectFactory;

    public function __construct(
        \Magento\Framework\Message\ManagerInterface        $messageManager,
        \Magento\Framework\Session\SessionManagerInterface $session,
        \Magento\Framework\UrlInterface                    $urlManager,
        \Magento\Framework\App\Response\RedirectInterface  $redirect,
        \Magecomp\Mobilelogin\Model\OtpFactory             $modelRegOtpFactory,
        MagecompHelper                                     $helperData,
        \Magento\Framework\App\RequestInterface            $request,
        \Magento\Framework\App\ResponseFactory             $responseFactory,
        UrlFactory       $urlFactory,
        RedirectFactory  $redirectFactory
    ) {
        $this->resultRedirectFactory = $redirectFactory;
        $this->messageManager = $messageManager;
        $this->session = $session;
        $this->_urlManager = $urlManager;
        $this->_helperData = $helperData;
        $this->redirect = $redirect;
        $this->_modelRegOtpFactory = $modelRegOtpFactory;
        $this->_responseFactory = $responseFactory;
        $this->_request = $request;
        $this->urlModel = $urlFactory->create();
    }

    public function aroundExecute(
        \Magento\Customer\Controller\Account\CreatePost $subject,
        \Closure                                        $proceed
    ) {
        if ($this->_helperData->isEnable()) {
            try {
                    $postdata = $this->_request->getParams();
                    $finalnumber = $postdata['mobilenumber'];

                    $otpModels = $this->_modelRegOtpFactory->create();
                    $collection = $otpModels->getCollection()
                        ->addFieldToFilter('mobile', $finalnumber)->getFirstItem();
                if ($collection->getData('is_verify') == '1') {
                    return $proceed();
                } else {
                    $this->messageManager->addError(
                        'Invalid OTP'
                    );
                    $defaultUrl = $this->urlModel->getUrl('*/*/create', ['_secure' => true]);
                    $resultRedirect = $this->resultRedirectFactory->create();
                    return $resultRedirect->setUrl($defaultUrl);
                }
            } catch (\Exception $e) {
                    $this->messageManager->addError($e->getMessage());
            }
        }

        return $proceed();
    }
}
