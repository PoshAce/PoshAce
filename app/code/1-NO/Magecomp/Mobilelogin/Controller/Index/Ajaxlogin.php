<?php
namespace Magecomp\Mobilelogin\Controller\Index;

use Magento\Framework\App\Action\Context;
use Magento\Customer\Model\Account\Redirect as AccountRedirect;
use Magento\Framework\Controller\ResultFactory;
use Magento\Customer\Model\Session;
use Magento\Framework\Data\Form\FormKey\Validator;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Framework\Stdlib\Cookie\CookieMetadataFactory;
use Magento\Framework\View\Result\PageFactory;
use Magento\Framework\Controller\Result\JsonFactory;
use Magento\Store\Model\StoreManagerInterface;
use Magecomp\Mobilelogin\Helper\Data as MagecompHelper;
use Magento\Catalog\Api\ProductRepositoryInterface; 
use Magento\Wishlist\Controller\WishlistProviderInterface;  
use Magento\Framework\Exception\LocalizedException; 
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Customer\Model\ResourceModel\Customer\CollectionFactory;

class Ajaxlogin extends \Magento\Framework\App\Action\Action
{
    protected $_resultPageFactory;
    protected $jsonResultFactory;
    protected $session;
    protected $formKeyValidator;
    protected $customerAccountManagement;
    private $cookieMetadataManager;
    private $scopeConfig;
    public $_storeManager;
    public $_helperdata;
    protected $productRepository;   
    protected $wishlistProvider;
    protected $customercollection;

    public function __construct(
        Context $context,
        PageFactory $resultPageFactory,
        JsonFactory $jsonResultFactory,
        Session $customerSession,
        Validator $formKeyValidator,
        AccountManagementInterface $customerAccountManagement,
        CookieMetadataFactory $cookieMetadataFactory,
        AccountRedirect $accountRedirect,
        StoreManagerInterface $storeManager,
        MagecompHelper $helperData,
        ProductRepositoryInterface $productRepository,  
        WishlistProviderInterface $wishlistProvider,
        CollectionFactory $customercollection
    ) {
        $this->_resultPageFactory = $resultPageFactory;
        $this->jsonResultFactory = $jsonResultFactory;
        $this->session = $customerSession;
        $this->formKeyValidator = $formKeyValidator;
        $this->customerAccountManagement = $customerAccountManagement;
        $this->cookieMetadataFactory = $cookieMetadataFactory;
        $this->accountRedirect = $accountRedirect;
        $this->_storeManager = $storeManager;
        $this->_helperdata = $helperData;
        $this->productRepository = $productRepository;  
        $this->wishlistProvider = $wishlistProvider;
        $this->customercollection =$customercollection;
        parent::__construct($context);
    }

    private function getCookieManager()
    {
        if (!$this->cookieMetadataManager) {
            $this->cookieMetadataManager = \Magento\Framework\App\ObjectManager::getInstance()->
            get(
                \Magento\Framework\Stdlib\Cookie\PhpCookieManager::class
            );
        }

        return $this->cookieMetadataManager;
    }

    private function getCookieMetadataFactory()
    {
        if (!$this->cookieMetadataFactory) {
            $this->cookieMetadataFactory = \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\Stdlib\Cookie\CookieMetadataFactory::class
            );
        }

        return $this->cookieMetadataFactory;
    }

    private function getScopeConfig()
    {
        if (!($this->scopeConfig instanceof \Magento\Framework\App\Config\ScopeConfigInterface)) {
            return \Magento\Framework\App\ObjectManager::getInstance()->get(
                \Magento\Framework\App\Config\ScopeConfigInterface::class
            );
        } else {
            return $this->scopeConfig;
        }
    }

    public function execute()
    {
        $error = false;
        $message = "";

       

        if ($this->session->isLoggedIn() || !$this->formKeyValidator->validate($this->getRequest())) {
            /** @var \Magento\Framework\Controller\Result\Redirect $resultRedirect */
            $resultRedirect = $this->resultRedirectFactory->create();
            $resultRedirect->setPath('*/*/');
        }

        if ($this->getRequest()->isPost()) {
            $baseUrl = $this->_storeManager->getStore()
                ->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
            $resultJson = $this->resultFactory->create(ResultFactory::TYPE_JSON);
            $login = $this->getRequest()->getPost('login');
            if (empty($login['username']) || empty($login['password'])) {
                $error = true;
                $message = __('Username or password not empty.');
                $this->messageManager->addError($message);
            }

            if (!$this->validate($login['username'])) {
                $error = true;
                $message = __('Invalid login or password.');
                $this->messageManager->addError($message);
                $response = [
                    'error' => $error,
                    'message' => $message,
                    'redirect' => $baseUrl
                ];
                $resultJson->setData($response);
                return $resultJson;
            }

            $objectManager = \Magento\Framework\App\ObjectManager::getInstance();
            if (is_numeric($login['username'])) {
               
                $collection = $this->customercollection->create()
                    ->addAttributeToFilter('mobilenumber', $login['username'])
                    ->load();
                if (count($collection) == 0) {
                    $message = __('Invalid login or password.');
                    $this->messageManager->addError($message);
                }

                foreach ($collection as $custom) {
                    $login['username'] = $custom->getEmail();
                }
            }

            if (!empty($login['username']) && !empty($login['password'])) {
                try {
                    if ($this->_helperdata->isEnableLoginEmail()) {
                        $this->_helperdata->sendMail($_SERVER['REMOTE_ADDR'], $login['username'], $_SERVER['HTTP_USER_AGENT']);
                    }

                    $customer = $this->customerAccountManagement->authenticate($login['username'], $login['password']);
                    $this->session->setCustomerDataAsLoggedIn($customer);
                    $this->session->regenerateId();
                    if ($this->getCookieManager()->getCookie('mage-cache-sessid')) {
                        $metadata = $this->getCookieMetadataFactory()->createCookieMetadata();
                        $metadata->setPath('/');
                    }

                    $redirectUrl = $this->accountRedirect->getRedirectCookie();
                    if (!$this->getScopeConfig()->getValue('customer/startup/redirect_dashboard') && $redirectUrl) {
                        $this->accountRedirect->clearRedirectCookie();
                        $resultRedirect = $this->resultRedirectFactory->create();
                        // URL is checked to be internal in $this->_redirect->success()
                        $resultRedirect->setUrl($this->_redirect->success($redirectUrl));
                        return $resultRedirect;
                    }
                } catch (\EmailNotConfirmedException $e) {
                    $value = $this->customerUrl->getEmailConfirmationUrl($login['username']);
                    $message = __(
                        'This account is not confirmed. <a href="%1">Click here</a> to resend confirmation email.',
                        $value
                    );
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\UserLockedException $e) {
                    $error = true;
                    $message = __(
                        'The account is locked. Please wait and try again or contact %1.',
                        $this->getScopeConfig()->getValue('contact/email/recipient_email')
                    );
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\AuthenticationException $e) {
                    $error = true;
                    $message = __('Invalid login or password.');
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\LocalizedException $e) {
                    $error = true;
                    $message = $e->getMessage();
                    $this->messageManager->addError($message);
                    $this->session->setUsername($login['username']);
                } catch (\Exception $e) {
                    $error = true;
                    $message = $e->getMessage();
                    $this->messageManager->addError(
                        __('An unspecified error occurred. Please contact us for assistance.')
                    );
                }
            }
        }
        if ($this->session->getBeforeWishlistRequest()) {   
            $requestParams = $this->session->getBeforeWishlistRequest();    
            $buyRequest = new \Magento\Framework\DataObject($requestParams);    
            $this->session->unsBeforeWishlistRequest(); 
        }   
        $productId = isset($requestParams['product']) ? (int)$requestParams['product'] : null;  
        try {
            $product = $this->productRepository->getById($productId);   
        } catch (NoSuchEntityException $e) {    
            $product = null;    
        }   
        $wishlist = $this->wishlistProvider->getWishlist();
        if($product) {  
            $result = $wishlist->addNewItem($product, $buyRequest); 
        }else{  
            $result=null;   
        }   
        if (is_string($result)) { 
            if ($wishlist->isObjectNew()) { 
                $wishlist->save();  
            }   
            $referer = $this->session->getBeforeWishlistUrl();  
            if ($referer) { 
                $this->session->setBeforeWishlistUrl(null); 
            } else {    
                $referer = $this->_redirect->getRefererUrl();   
            }   
            $this->_objectManager->get(\Magento\Wishlist\Helper\Data::class)->calculate();  
            $this->messageManager->addComplexSuccessMessage(    
                'addProductSuccessMessage', 
                [   
                    'product_name' => $product->getName(),  
                    'referer' => $referer   
                ]   
            );  
        }
        $response = [
            'error' => $error,
            'message' => $message,
            'redirect' => $baseUrl
        ];
        $resultJson->setData($response);
        return $resultJson;
    }
    public function validate($username)
    {
        $pattern = '/^((\+[1-9]{1,4}[ \-]*)|(\([0-9]{2,3}\)[ \-]*)|([0-9]{2,4})[ \-]*)*?[0-9]{3,4}?[ \-]*[0-9]{3,4}?$/';
        $emailPattern = '/^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$/';

        if (!is_numeric($username)) {
            if (preg_match($emailPattern, $username)) {
                return true;
            } else {
                return false;
            }
        }

        if (preg_match($pattern, $username)) {
            if (strlen($username) >= 10 && strlen($username) == 12) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
