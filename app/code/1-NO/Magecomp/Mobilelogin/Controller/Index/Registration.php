<?php

namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\Url as CustomerUrl;
use Magento\Framework\App\Action\Context;
use Magento\Framework\App\Action\HttpPostActionInterface;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Framework\UrlFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\Registration as RegistrationModal;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Framework\App\Action\Action;

/**
 * Class Registration
 * Magecomp\Mobilelogin\Controller\Index
 */
class Registration extends Action implements HttpPostActionInterface
{
    /**
     * @var JsonFactory
     */
    protected $_jsonResultFactory;

    /**
     * @var StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var MagecompHelper
     */
    protected $_helperData;
    protected $accountManagement;
    protected $customerUrl;
    protected $urlModel;

    /**
     * Registration constructor.
     * @param Context $context
     * @param JsonFactory $jsonResultFactory
     * @param StoreManagerInterface $storeManager
     * @param MagecompHelper $helperData
     * @param RegistrationModal $registration
     * @param CustomerExtractor $customerExtractor
     * @param Session $customerSession
     */
    public function __construct(
        Context                    $context,
        JsonFactory                $jsonResultFactory,
        StoreManagerInterface      $storeManager,
        MagecompHelper             $helperData,
        RegistrationModal          $registration,
        CustomerExtractor          $customerExtractor,
        Session                    $customerSession,
        AccountManagementInterface $accountManagement,
        CustomerUrl                $customerUrl,
        UrlFactory                 $urlFactory
    ) {
        $this->_jsonResultFactory = $jsonResultFactory;
        $this->_storeManager = $storeManager;
        $this->_helperData = $helperData;
        $this->session = $customerSession;
        $this->registration = $registration;
        $this->customerExtractor = $customerExtractor;
        $this->accountManagement = $accountManagement;
        $this->customerUrl = $customerUrl;
        $this->urlModel = $urlFactory->create();
        parent::__construct($context);
    }

    /**
     * @return \Magento\Framework\App\ResponseInterface|\Magento\Framework\Controller\Result\Json|\Magento\Framework\Controller\ResultInterface
     */
    public function execute()
    {
        try {
            if (!$this->getRequest()->isPost()) {
                $jsonOutput = ["status" => false, "message" => __("This operation is not permitted.")];
            } else {
                if ($this->session->isLoggedIn() || !$this->registration->isAllowed()) {
                    $jsonOutput = ["status" => false, "message" => __("There is some issue. Please try after some time.")];
                } else {
                    $websiteId = $this->_storeManager->getWebsite()->getId();
                    $data = $this->getRequest()->getParams();

                    $data['mobile'] = $data['mobilenumber'];
                    $data['otp'] = $data['otp'];
                    $isVerified = $this->_helperData->checkOTPisVerified(
                        $data,
                        $this->_helperData::REGISTRATION_OTP_TYPE,
                        $websiteId
                    );

                    if (count($isVerified) == 1) {
                        $customer = $this->customerExtractor->extract('customer_account_create', $this->_request);
                        $customer->setAddresses([]);
                        ///$redirectUrl = $this->session->getBeforeAuthUrl();
                        $redirectUrl = $this->_helperData->getAfterLoginRedirect();


                        $customerCollection = $this->_helperData->checkCustomerExists($customer->getEmail(), "email", $websiteId);
                        if (count($customerCollection) > 0) {
                            $jsonOutput = ["status" => false, "message" => __("Customer already registered with same EmailId")];
                            $jsonResult = $this->_jsonResultFactory->create();
                            $jsonResult->setData($jsonOutput);
                            return $jsonResult;
                        }

                        $customer = $this->_helperData->createAccount($customer, $data, $redirectUrl, $websiteId);
                        $this->_eventManager->dispatch(
                            'customer_register_success',
                            ['account_controller' => $this, 'customer' => $customer]
                        );
                        $confirmationStatus = $this->accountManagement->getConfirmationStatus($customer->getId());
                        if ($confirmationStatus === AccountManagementInterface::ACCOUNT_CONFIRMATION_REQUIRED) {
                            $this->messageManager->addComplexSuccessMessage(
                                'confirmAccountSuccessMessage',
                                [
                                    'url' => $this->customerUrl->getEmailConfirmationUrl($customer->getEmail()),
                                ]
                            );
                            $redirectUrl = $this->urlModel->getUrl('customer/account/login/', ['_secure' => true]);
                            $resultRedirect = $this->resultRedirectFactory->create();
                            $resultRedirect->setUrl($this->_redirect->success($redirectUrl));
                        } else {
                            $this->session->setCustomerDataAsLoggedIn($customer);
                        }
                    } else {
                        $jsonOutput = ["status" => false, "message" => __("Mobile no is not verified.")];
                    }

                    $jsonOutput = ["status" => true, "redirectUrl" => $redirectUrl];
                }
            }
        } catch (\Exception $e) {
            $jsonOutput = ["status" => false, "message" => __("There is some issue. Please try after some time.")];
        }

        $jsonResult = $this->_jsonResultFactory->create();
        $jsonResult->setData($jsonOutput);
        return $jsonResult;
    }
}
