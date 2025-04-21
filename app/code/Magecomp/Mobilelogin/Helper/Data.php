<?php
namespace Magecomp\Mobilelogin\Helper;

use Magento\Framework\Mail\Template\TransportBuilder;
use Magento\Framework\Translate\Inline\StateInterface;
use Magento\Framework\HTTP\PhpEnvironment\RemoteAddress;
use Magento\Customer\Model\CustomerFactory;
use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Store\Model\StoreManagerInterface;
use Magento\Customer\Model\ResourceModel\Customer\Collection as CustomerCollection;
use Magento\Store\Model\ScopeInterface;
use Magento\Customer\Api\AccountManagementInterface;
use Magento\Customer\Model\CustomerExtractor;
use Magento\Customer\Model\Session;
use Magento\Customer\Model\AccountManagement;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Framework\Exception\SecurityViolationException;
use Magento\Framework\Escaper;
use Magento\Framework\Stdlib\DateTime\TimezoneInterface;
use Magento\Framework\UrlFactory;

/**
 * Class Data
 * Magecomp\Mobilelogin\Helper
 */
class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION = 'mobilelogin/generalsettings/adminemailtemplate';
    const XML_PATH_EMAIL_ADMIN_QUOTE_SENDER = 'mobilelogin/generalsettings/adminemailsender';

    const MOBILELOGIN_MODULEOPTION_ENABLE = 'mobilelogin/moduleoption/enable';
    const EMAIL_CONFIRMATION_ENABLE = 'customer/create_account/confirm';

    const MOBILELOGIN_GENERALSETTINGS_OTPTYPE = 'mobilelogin/generalsettings/otptype';
    const MOBILELOGIN_GENERALSETTINGS_OTP = 'mobilelogin/generalsettings/otp';
    const MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY = 'mobilelogin/generalsettings/loginnotify';
    const MOBILELOGIN_OTPSEND_MESSAGE = 'mobilelogin/otpsend/message';
    const MOBILELOGIN_REG_DLTID = 'mobilelogin/otpsend/msg91dltidreg';
    const MOBILELOGIN_LOGIN_DLTID = 'mobilelogin/loginotpsend/msg91dltidlogin';
    const MOBILELOGIN_LOGIN_MSG = 'mobilelogin/loginotpsend/message';


    const MOBILELOGIN_REG_COMPAIGNID='mobilelogin/otpsend/tmp_idreg';
    const MOBILELOGIN_LOGIN_COMPAIGNID='mobilelogin/loginotpsend/tmp_idlogin';
    const MOBILELOGIN_FORGOT_COMPAIGNID = 'mobilelogin/forgototpsend/tmp_idforgot';
    const MOBILELOGIN_UPDATE_COMPAIGNID = 'mobilelogin/updateotpsend/tmp_idupdate';

    const MOBILELOGIN_FORGOT_DLTID = 'mobilelogin/forgototpsend/msg91dltidforgot';
    const MOBILELOGIN_FORGOT_MSG = 'mobilelogin/forgototpsend/message';

    const MOBILELOGIN_UPDATE_DLTID = 'mobilelogin/updateotpsend/msg91dltidupdate';


    const MOBILELOGIN_UPDATEOTPSEND_MESSAGE = 'mobilelogin/updateotpsend/message';

    const MOBILELOGIN_DESIGN_TEMPLATEREG  =  'mobilelogin/design/templatereg';
    const MOBILELOGIN_DESIGN_TEMPLATELOGIN  =  'mobilelogin/design/templatelogin';
    const MOBILELOGIN_DESIGN_TEMPLATEFORGOT  =  'mobilelogin/design/templateforgot';
    const MOBILELOGIN_DESIGN_MAINLAYOUT    = 'mobilelogin/design/mainlayout';
    const MOBILELOGIN_DESIGN_LAYOUT  =    'mobilelogin/design/layout';
    const MOBILELOGIN_DESIGN_IMAGEREG = 'mobilelogin/design/imagereg';
    const MOBILELOGIN_DESIGN_IMAGELOGIN = 'mobilelogin/design/imagelogin';
    const MOBILELOGIN_DESIGN_IMAGEFORGOT = 'mobilelogin/design/imageforgot';
    const COUNTRY_CODE_PATH = 'general/country/default';
    const COUNTRY_CODE_ALLOW = 'general/country/allow';

    const MOBILELOGIN_TEST_MOBILE = 'mobilelogin/testapi/testmobile';
    const MOBILELOGIN_TEST_MSG = 'mobilelogin/testapi/testmessage';
    const MOBILELOGIN_TEST_DLTID = 'mobilelogin/testapi/testdltid';
    const MOBILELOGIN_TEST_COMPAIGNID = 'mobilelogin/testapi/testcompignid';

    const REGISTRATION_OTP_TYPE = "REGISTER";
    const UPDATE_OTP_TYPE = "UPDATEMNO";
    const LOGIN_OTP_TYPE = "LOGIN";
    const FORGOTPASSWORD_OTP_TYPE = "FORGOT";

    const MOBILELOGIN_WHICHAPIUSING ='mobilelogin/smsgatways/whichapiusing';

    /**
     * @var ScopeConfigInterface
     */
    protected $scopeConfig;



    /**
     * @var \Magento\Store\Model\StoreManagerInterface
     */
    protected $_storeManager;

    /**
     * @var \Magento\Framework\Json\Helper\Data
     */
    protected $jsonHelper;
    protected $_encryptor;
    protected $inlineTranslation;
    protected $transportBuilder;
    protected $escaper;
    protected $urlFactory;
    protected $customerSession;

    /**
     * @var \Magento\Framework\Filesystem\Driver\Http
     */
    protected $httpFile;

    /**
     * @var \Magento\Customer\Model\ResourceModel\Customer\Collection
     */
    protected $customerCollection;

    /**
     * @var RemoteAddress
     */
    protected $remoteAddress;

    /**
     * @var Apicall
     */
    protected $apicall;

    /**
     * @var CustomerFactory
     */
    protected $_customerFactory;

    /**
     * @var TimezoneInterface
     */
    protected $timezoneInterface;
    protected $objectManager;
	protected $_otpModal;
	protected $_otpCollection;
	protected $accountManagement;
	protected $customerExtractor;
	protected $session;

    /**
     * Data constructor.
     * @param ScopeConfigInterface $scopeConfig
     * @param StoreManagerInterface $storeManager
     * @param \Magento\Framework\Json\Helper\Data $jsonHelper
     * @param \Magento\Framework\Filesystem\Driver\Http $httpFile
     * @param CustomerCollection $customerCollection
     * @param RemoteAddress $remoteAddress
     * @param Apicall $apicall
     * @param CustomerFactory $CustomerFactory
     * @param \Magecomp\Mobilelogin\Model\OtpFactory $otp
     * @param AccountManagementInterface $accountManagement
     * @param CustomerExtractor $customerExtractor
     * @param Session $customerSession
     * @param \Magento\Framework\Encryption\EncryptorInterface $encryptor
     * @param StateInterface $inlineTranslation
     * @param TransportBuilder $transportBuilder
     * @param Escaper $escaper
     * @param TimezoneInterface $timezoneInterface
     */
    public function __construct(
        ScopeConfigInterface $scopeConfig,
        StoreManagerInterface $storeManager,
        \Magento\Framework\Json\Helper\Data $jsonHelper,
        \Magento\Framework\Filesystem\Driver\Http $httpFile,
        CustomerCollection $customerCollection,
        RemoteAddress $remoteAddress,
        Apicall $apicall,
        CustomerFactory $customerFactory,
        \Magento\Framework\ObjectManagerInterface $objectManager,
        \Magecomp\Mobilelogin\Model\OtpFactory $otp,
        \Magecomp\Mobilelogin\Model\ResourceModel\Otp\CollectionFactory $otpCollection,
        AccountManagementInterface $accountManagement,
        CustomerExtractor $customerExtractor,
        Session $customerSession,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        StateInterface $inlineTranslation,
        TransportBuilder $transportBuilder,
        Escaper $escaper,
        TimezoneInterface $timezoneInterface,
        UrlFactory $urlFactory,
        Session $session
        
    ) {
        $this->scopeConfig = $scopeConfig;
        $this->_storeManager = $storeManager;
        $this->jsonHelper = $jsonHelper;
        $this->httpFile = $httpFile;
        $this->customerCollection = $customerCollection;
        $this->objectManager = $objectManager;
        $this->remoteAddress = $remoteAddress;
        $this->apicall = $apicall;
        $this->_customerFactory = $customerFactory;
        $this->_otpModal = $otp;
        $this->_otpCollection = $otpCollection;
        $this->accountManagement = $accountManagement;
        $this->customerExtractor = $customerExtractor;
        $this->session = $customerSession;
        $this->_encryptor = $encryptor;
        $this->inlineTranslation = $inlineTranslation;
        $this->transportBuilder = $transportBuilder;
        $this->escaper = $escaper;
        $this->timezoneInterface = $timezoneInterface;
        $this->urlFactory = $urlFactory;
        $this->customerSession = $session;


    }

    /**
     * @return mixed
     */
    public function isMobileEnable()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_MODULEOPTION_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function isLoginEmailConfirm()
    {
        return $this->scopeConfig->getValue(
            self::EMAIL_CONFIRMATION_ENABLE,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getWhichApiUse()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_WHICHAPIUSING,
            ScopeInterface::SCOPE_STORE
        );
    }

    public function getregdlt()
    {
        if($this->getWhichApiUse()=='campaign'){
            return $this->scopeConfig->getValue(self::MOBILELOGIN_REG_COMPAIGNID,ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_REG_DLTID,
            ScopeInterface::SCOPE_STORE
        );
    }

  

    public function getlogindlt()
    {
        if($this->getWhichApiUse()=='campaign'){
            return $this->scopeConfig->getValue(self::MOBILELOGIN_LOGIN_COMPAIGNID,ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_LOGIN_DLTID,
            ScopeInterface::SCOPE_STORE
        );
    }
    
   



    public function getforgotdlt()
    {
        if($this->getWhichApiUse()=='campaign'){
            return $this->scopeConfig->getValue(self::MOBILELOGIN_FORGOT_COMPAIGNID,ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_FORGOT_DLTID,
            ScopeInterface::SCOPE_STORE
        );
    }

 

    public function getupdatedlt()
    {
        if($this->getWhichApiUse()=='campaign'){
            return $this->scopeConfig->getValue(self::MOBILELOGIN_UPDATE_COMPAIGNID,ScopeInterface::SCOPE_STORE);
        }
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_UPDATE_DLTID,
            ScopeInterface::SCOPE_STORE
        );
    }



    /**
     * @return mixed
     */
    public function getGeoCountryCode()
    {
        try {
            $ipData = $this->jsonHelper->jsonDecode(
                $this->httpFile->fileGetContents(
                    "www.geoplugin.net/json.gp?ip=".$this->remoteAddress->getRemoteAddress()
                )
            );
            return $ipData->geoplugin_countryCode;
        } catch (\Exception $e) {
            return $this->scopeConfig->getValue(
                self::COUNTRY_CODE_PATH,
                ScopeInterface::SCOPE_WEBSITES
            );
        }
    }

    /**
     * @return mixed
     */
    public function getDefaultCountry()
    {
        return $this->scopeConfig->getValue(
            self::COUNTRY_CODE_PATH,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @return mixed
     */
    public function getAllowCountry()
    {
        return $this->scopeConfig->getValue(
            self::COUNTRY_CODE_ALLOW,
            ScopeInterface::SCOPE_WEBSITES
        );
    }

    /**
     * @param bool $isArray
     * @return array|string
     */
    public function getApplicableCountry($isArray = true)
    {
        $_defaultCountry = [];
        $_country = [];
        $_defaultCountry[] = $this->getDefaultCountry();
        $_country = array_merge($_defaultCountry, explode(",", $this->getAllowCountry()));
        if (!$isArray) {
            $_country = $this->jsonHelper->jsonEncode($_country);
        }

        return $_country;
    }

    /**
     * @return bool
     */
    public function isEnable()
    {
        if ($this->isMobileEnable()) {
            $geoCountryCode = $this->getGeoCountryCode();
            if (!in_array($geoCountryCode, $this->getApplicableCountry())) {
                return false;
            }

            return true;
        }

        return false;
    }

    /**
     * @return int
     */
    public function getLayoutType()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout == 'ultimatelayout') {
            return 1;
        }
    }

    /**
     * @return mixed
     */
    public function getOtpStringlenght()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_OTP,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed|string
     */
    public function getRegTemplateImage()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout != 'ultimatelayout') {
            return "";
        }

        $designLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designLayout == 'template') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_TEMPLATEREG,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($designLayout == 'image') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_IMAGEREG,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }
    public function getLoginTemplateImage()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout != 'ultimatelayout') {
            return "";
        }

        $designLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designLayout == 'template') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_TEMPLATELOGIN,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($designLayout == 'image') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_IMAGELOGIN,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }
    public function getForgotTemplateImage()
    {
        $designMainLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_MAINLAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designMainLayout != 'ultimatelayout') {
            return "";
        }

        $designLayout = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($designLayout == 'template') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_TEMPLATEFORGOT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        } elseif ($designLayout == 'image') {
            return $image =  $this->scopeConfig->getValue(
                self::MOBILELOGIN_DESIGN_IMAGEFORGOT,
                \Magento\Store\Model\ScopeInterface::SCOPE_STORE
            );
        }
    }

    /**
     * @return int
     */
    public function getImageType()
    {
        $imageType = $this->scopeConfig->getValue(
            self::MOBILELOGIN_DESIGN_LAYOUT,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
        if ($imageType == 'template') {
            return 1;
        } else {
            return 0;
        }
    }

    /***
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreId()
    {
        return $this->_storeManager->getStore()->getId();
    }

    /**
     * @return int
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getWebsiteId()
    {
        return $this->_storeManager->getStore()->getWebsiteId();
    }

    /**
     * @param bool $fromStore
     * @return mixed
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreUrl($fromStore = true)
    {
        return $this->_storeManager->getStore()->getUrl();
    }

    /**
     * @return string
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getStoreName()
    {
        return $this->_storeManager->getStore()->getName();
    }

    /**
     * @return mixed
     */
    public function isEnableLoginEmail()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_LOGINNOTIFY,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $remoteId
     * @param $mail
     * @param $userAgent
     * @return string
     * @throws \Magento\Framework\Exception\LocalizedException
     * @throws \Magento\Framework\Exception\MailException
     */
    public function sendMail($remoteId, $mail, $userAgent)
    {
        // Send Mail To Admin For This
        $date = $this->timezoneInterface->date()->format('Y-m-d H:i:s');

        $browser = $this->get_browser_name($userAgent);
        $this->inlineTranslation->suspend();
        $storeScope = \Magento\Store\Model\ScopeInterface::SCOPE_STORE;
        $transport = $this->transportBuilder
            ->setTemplateIdentifier(
                $this->scopeConfig->getValue(self::XML_PATH_EMAIL_ADMIN_QUOTE_NOTIFICATION, $storeScope)
            )
            ->setTemplateOptions(
                [
                    'area' => 'frontend',
                    'store' => \Magento\Store\Model\Store::DEFAULT_STORE_ID,
                ]
            )
            ->setTemplateVars(
                [
                'ip'  => $remoteId,
                'email' => $mail,
                'datetime' => $date,
                'browser' => $browser,
                'subject' => "We detected an attempt to access your account"
                ]
            )
            ->setFrom($this->scopeConfig->getValue(self::XML_PATH_EMAIL_ADMIN_QUOTE_SENDER, $storeScope))
            ->addTo($mail)
            ->getTransport();

        $transport->sendMessage();
        $this->inlineTranslation->resume();
        return "true";
    }
    public function get_browser_name($user_agent)
    {
        // Make case insensitive.
        $t = strtolower($user_agent);

        // If the string starts with the string, strpos returns 0 (i.e., FALSE). Do a ghetto hack and start with a space.
        // "[strpos()] may return Boolean FALSE, but may also return a non-Boolean value which evaluates to FALSE."
        //     http://php.net/manual/en/function.strpos.php
        $t = " " . $t;

        // Humans / Regular Users
        if (strpos($t, 'opera')!==false || strpos($t, 'opr/')!==false) {
            return 'Opera';
        } elseif (strpos($t, 'edge')!==false) {
            return 'Edge';
        } elseif (strpos($t, 'chrome')!==false) {
            return 'Chrome';
        } elseif (strpos($t, 'safari')!==false) {
            return 'Safari';
        } elseif (strpos($t, 'firefox')!==false) {
            return 'Firefox';
        } elseif (strpos($t, 'msie')!==false || strpos($t, 'trident/7')!==false) {
            return 'Internet Explorer';
        }

        // Search Engines
        elseif (strpos($t, 'google')!==false) {
            return '[Bot] Googlebot';
        } elseif (strpos($t, 'bing') !==false) {
            return '[Bot] Bingbot';
        } elseif (strpos($t, 'slurp')!==false) {
            return '[Bot] Yahoo! Slurp';
        } elseif (strpos($t, 'duckduckgo')!==false) {
            return '[Bot] DuckDuckBot' ;
        } elseif (strpos($t, 'baidu')!==false) {
            return '[Bot] Baidu';
        } elseif (strpos($t, 'yandex')!==false) {
            return '[Bot] Yandex';
        } elseif (strpos($t, 'sogou')!==false) {
            return '[Bot] Sogou';
        } elseif (strpos($t, 'exabot')!==false) {
            return '[Bot] Exabot';
        } elseif (strpos($t, 'msn')!==false) {
            return '[Bot] MSN';
        }

        // Common Tools and Bots
        elseif (strpos($t, 'mj12bot')!==false) {
            return '[Bot] Majestic';
        } elseif (strpos($t, 'ahrefs')!==false) {
            return '[Bot] Ahrefs';
        } elseif (strpos($t, 'semrush')!==false) {
            return '[Bot] SEMRush';
        } elseif (strpos($t, 'rogerbot')!==false || strpos($t, 'dotbot')!==false) {
            return '[Bot] Moz or OpenSiteExplorer';
        } elseif (strpos($t, 'frog')!==false || strpos($t, 'screaming')!==false) {
            return '[Bot] Screaming Frog';
        }

        // Miscellaneous
        elseif (strpos($t, 'facebook')!==false) {
            return '[Bot] Facebook';
        } elseif (strpos($t, 'pinterest')!==false) {
            return '[Bot] Pinterest';
        }

        // Check for strings commonly used in bot user agents
        elseif (strpos($t, 'crawler')!==false || strpos($t, 'api')!==false ||
            strpos($t, 'spider')!==false || strpos($t, 'http')!==false ||
            strpos($t, 'bot')!==false || strpos($t, 'archive')!==false ||
            strpos($t, 'info')!==false || strpos($t, 'data')!==false) {
            return '[Bot] Other';
        }

        return 'Other (Unknown)';
    }

    /**
     * @return mixed
     */
    public function getOtpStringtype()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_GENERALSETTINGS_OTPTYPE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return mixed
     */
    public function getRegOtpTemplate()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_OTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @return false|string
     */
    public function generateRandomString()
    {
        $length = $this->getOtpStringlenght();
        if ($this->getOtpStringtype() == "N") {
            $randomString = substr(str_shuffle("0123456789"), 0, $length);
        } else {
            $randomString = substr(
                str_shuffle("0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ"),
                0,
                $length
            );
        }

        return $randomString;
    }

    /**
     * @param $mobile
     * @param $randomCode
     * @return mixed|string|string[]
     * @throws \Magento\Framework\Exception\NoSuchEntityException
     */
    public function getRegOtpMessage($mobile, $randomCode)
    {
        $storeName = $this->getStoreName();
        $storeUrl = $this->getStoreUrl();
        $codes = ['{{shop_name}}','{{shop_url}}','{{random_code}}'];
        $accurate = [$storeName,$storeUrl,$randomCode];
        return str_replace($codes, $accurate, $this->getRegOtpTemplate());
    }

    public function getAfterLoginRedirect()
    {
return $this->_storeManager->getStore()->getBaseUrl(\Magento\Framework\UrlInterface::URL_TYPE_LINK);
    }

    /**
     * @param $fieldValue
     * @param string $fieldType
     * @param int $websiteId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function checkCustomerExists($fieldValue, $fieldType = "mobile", $websiteId = 1)
    {
        $collection = $this->_customerFactory->create()->getCollection();
        if ($fieldType == "mobile") {
            $collection->addFieldToFilter("mobilenumber", $fieldValue);
        }

        if ($fieldType == "email") {
            $collection->addFieldToFilter("email", $fieldValue);
        }

        $collection->addAttributeToFilter('website_id', $websiteId);
        return $collection;
    }

    /**
     * @param $mobile
     * @param int $websiteId
     * @return bool
     */
    public function checkCustomerWithSameMobileNo($mobile, $websiteId = 1)
    {
        $customer = $this->checkCustomerExists($mobile, "mobile", $websiteId);
        if (count($customer) > 0) {
            return true;
        }

        return false;
    }

    public function sendUpdateOTPCode($data, $websiteId = 1)
    {

        try {
            if ($this->checkCustomerWithSameMobileNo($data, $websiteId)) {
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }

            $randomCode = $this->generateRandomString();
            $message = $this->getUpdateOtpMessage($data, $randomCode);
            $dlt = $this->getupdatedlt();
          
            $otpModel = $this->_otpModal->create();

            $collection = $this->checkOTPExists($data, self::UPDATE_OTP_TYPE, $websiteId);

            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }

            $otpModel->setType(self::UPDATE_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->callApiUrl($message, $data, $dlt);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $randomCode
     * @return string
     */
    public function encryptOtp($randomCode)
    {
        return $this->_encryptor->encrypt($randomCode);
    }

    /**
     * @param $randomCode
     * @return string
     */
    public function decryptOtp($randomCode)
    {
        return $this->_encryptor->decrypt($randomCode);
    }

    /**
     * @param $mobile
     * @param string $type
     * @param int $websiteId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function checkOTPExists($mobile, $type = "", $websiteId = 1)
    {
        return $this->_otpCollection->create()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('mobile', $mobile)
            ->addFieldToFilter('is_verify', 0)
            ->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $data
     * @param string $type
     * @param int $websiteId
     * @return \Magento\Framework\Data\Collection\AbstractDb|\Magento\Framework\Model\ResourceModel\Db\Collection\AbstractCollection|null
     */
    public function checkOTPisVerified($data, $type = "", $websiteId = 1)
    {
        if (!$websiteId) {
            $websiteId = 1;
        }


        return $this->_otpCollection->create()
            ->addFieldToFilter('type', $type)
            ->addFieldToFilter('mobile', $data['mobile'])
            ->addFieldToFilter('random_code', $data['otp'])
            ->addFieldToFilter('is_verify', 1)
            ->addFieldToFilter('website_id', $websiteId);
    }

    /**
     * @param $message
     * @param $mobilenumbers
     * @return array|bool[]|string
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function callApiUrl($message, $mobilenumbers, $dlt)
    {
        return $this->apicall->callApiUrl($message, $mobilenumbers, $dlt);
    }

    public function getUpdateOtpMessage($mobile, $randomCode)
    {
        $storeName = $this->getStoreName();
        $storeUrl = $this->getStoreUrl();
        $codes = ['{{shop_name}}','{{shop_url}}','{{random_code}}'];
        $accurate = [$storeName,$storeUrl,$randomCode];
        return str_replace($codes, $accurate, $this->getUpdateOtpTemplate());
    }
    public function getLoginOtpMessage($mobile, $randomCode)
    {
        $storeName = $this->getStoreName();
        $storeUrl = $this->getStoreUrl();
        $codes = ['{{shop_name}}','{{shop_url}}','{{random_code}}'];
        $accurate = [$storeName,$storeUrl,$randomCode];
        return str_replace($codes, $accurate, $this->getLoginOtpTemplate());
    }public function getForgotOtpMessage($mobile, $randomCode)
    {
        $storeName = $this->getStoreName();
        $storeUrl = $this->getStoreUrl();
        $codes = ['{{shop_name}}','{{shop_url}}','{{random_code}}'];
        $accurate = [$storeName,$storeUrl,$randomCode];
        return str_replace($codes, $accurate, $this->getForgotOtpTemplate());
    }
    public function getUpdateOtpTemplate()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_UPDATEOTPSEND_MESSAGE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getLoginOtpTemplate()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_LOGIN_MSG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }
    public function getForgotOtpTemplate()
    {
        return $this->scopeConfig->getValue(
            self::MOBILELOGIN_FORGOT_MSG,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE
        );
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]|string
     */
    public function sendRegistrationOTP($data, $websiteId = 1)
    {
        try {

            

            if ($this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
              
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }

         

            $randomCode = $this->generateRandomString();
            $message = $this->getRegOtpMessage($data['mobile'], $randomCode);
            $dlt = $this->getregdlt();
         


            $otpModel = $this->_otpModal->create();

            $collection = $this->checkOTPExists($data['mobile'], self::REGISTRATION_OTP_TYPE, $websiteId);

            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }

            $otpModel->setType(self::REGISTRATION_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data['mobile']);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->callApiUrl($message, $data['mobile'], $dlt);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyRegistrationOTP($data, $websiteId = 1)
    {
        try {
            if ($this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }

            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::REGISTRATION_OTP_TYPE)
                ->addFieldToFilter('website_id', $websiteId);


            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true,"message"=>__("Successfully Verify Registration OTP.")];
            }

            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }
    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyUpdateMobilenumberOTP($data, $websiteId = 1)
    {
        try {
            if ($this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer already exists.")];
            }


            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::UPDATE_OTP_TYPE)
                ->addFieldToFilter('website_id', $websiteId);


            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true];
            }

            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    public function getcustomerlogin()
    {
        if ( $this->customerSession->isLoggedIn()) 
        {
            return true;
        }
        else
        {
           return false;
        }
    }

    /**
     * @param $customer
     * @param $data
     * @param $redirectUrl
     * @param $websiteId
     * @return \Magento\Customer\Api\Data\CustomerInterface
     */
    public function createAccount($customer, $data, $redirectUrl, $websiteId)
    {
        try {
            $customer = $this->accountManagement
                ->createAccount($customer, $data['password'], $redirectUrl);
            return $customer;
        } catch (\Exception $e) {
            throw new \Exception(
                __('There is some issue. Please try after some time.')
            );
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]|string
     */
    public function sendLoginOTP($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['loginotpmob'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }

            $randomCode = $this->generateRandomString();
            $message = $this->getLoginOtpMessage($data['loginotpmob'], $randomCode);
            $dlt = $this->getlogindlt();
          
            $otpModel = $this->_otpModal->create();
            $collection = $this->checkOTPExists($data['loginotpmob'], self::LOGIN_OTP_TYPE, $websiteId);
            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }

            $otpModel->setType(self::LOGIN_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data['loginotpmob']);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->callApiUrl($message, $data['loginotpmob'], $dlt);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyLoginOTP($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("ss Customer does not exists.")];
            }

            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::LOGIN_OTP_TYPE)
                ->addFieldToFilter('is_verify', 0)
                ->addFieldToFilter('website_id', $websiteId);

            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true];
            }

            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }
    public function sendOTPCode($mobile, $websiteId = 1)
    {
        try {
            if ($this->checkCustomerWithSameMobileNo($mobile, $websiteId)) {
                return ["status"=>"exist", "message"=>__("Customer already exists.")];
            }

            $otpModels = $this->_otpModal->create();
            $collection = $otpModels->getCollection();
            $collection->addFieldToFilter('mobile', $mobile);

            $objDate = $this->objectManager->create('Magento\Framework\Stdlib\DateTime\DateTime');
            $date = $objDate->gmtDate();
            $randomCode = $this->generateRandomString();
            $message = $this->getRegOtpMessage($mobile, $randomCode);
            $dlt = $this->getregdlt();
           
            if (count($collection) == 0) {
                $otpModel = $this->_otpModal->create();
                $otpModel->setRandomCode($randomCode);
                $otpModel->setCreatedTime($date);
                $otpModel->setIsVerify(0);
                $otpModel->setMobile($mobile);
                $otpModel->setType(self::REGISTRATION_OTP_TYPE);
                $otpModel->setWebsiteId($websiteId);
                $otpModel->save();
            } else {
                $otpModel = $this->_otpModal->create()->load($mobile, 'mobile');
                $otpModel->setRandomCode($randomCode);
                $otpModel->setCreatedTime($date);
                $otpModel->setIsVerify(0);
                $otpModel->setMobile($mobile);
                $otpModel->setType(self::REGISTRATION_OTP_TYPE);
                $otpModel->setWebsiteId($websiteId);
                $otpModel->save();
            }

            $apiReturn = $this->callApiUrl($message, $mobile, $dlt);
            return $apiReturn;
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /**
     * @param $data
     * @param $websiteId
     * @return array|bool[]|string
     */
    public function sendForgotPasswordOTP($data, $websiteId)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['forgotmob'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }

            $randomCode = $this->generateRandomString();
            $message = $this->getForgotOtpMessage($data['forgotmob'], $randomCode);
            $dlt = $this->getforgotdlt();
           
            $otpModel = $this->_otpModal->create();

            $collection = $this->checkOTPExists($data['forgotmob'], self::FORGOTPASSWORD_OTP_TYPE, $websiteId);

            if (count($collection) > 0) {
                $otpModel = $collection->getFirstItem();
            }

            $otpModel->setType(self::FORGOTPASSWORD_OTP_TYPE);
            $otpModel->setRandomCode($randomCode);
            $otpModel->setIsVerify(0);
            $otpModel->setMobile($data['forgotmob']);
            $otpModel->setWebsiteId($websiteId);
            $otpModel->save();
            return $this->callApiUrl($message, $data['forgotmob'], $dlt);
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }

    /***
     * @param $data
     * @param int $websiteId
     * @return array
     */
    public function sendForgotPasswordEmail($data, $websiteId = 1)
    {
        try {
            if (!\Zend_Validate::is($data['email'], \Magento\Framework\Validator\EmailAddress::class)) {
                $this->session->setForgottenEmail($data['email']);
                return ["status"=>false, "message"=>__("Please enter correct email address.")];
            }

            if (count($this->checkCustomerExists($data['email'], "email", $websiteId)) <= 0) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }

            $this->accountManagement->initiatePasswordReset(
                $data['email'],
                AccountManagement::EMAIL_RESET
            );
            return ["status"=>true, "message"=>
                __('If there is an account associated with %1 you will receive an email with a link to reset your password.', $this->escaper->escapeHtml($data['email']))];
        } catch (NoSuchEntityException $exception) {
            return ["status"=>false, "message"=> $exception->getMessage()];
        } catch (SecurityViolationException $exception) {
            return ["status"=>false, "message"=> $exception->getMessage()];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>'We\'re unable to send the password reset email.'];
        }
    }

    /**
     * @param $data
     * @param int $websiteId
     * @return array|bool[]
     */
    public function verifyForgotPasswordOTP($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }

            $collection = $this->_otpCollection->create()
                ->addFieldToFilter('mobile', $data['mobile'])
                ->addFieldToFilter('random_code', $data['verifyotp'])
                ->addFieldToFilter('type', self::FORGOTPASSWORD_OTP_TYPE)
                ->addFieldToFilter('website_id', $websiteId);

            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $item->setIsVerify(1);
                $item->save();
                return ["status"=>true];
            }

            return ["status"=>false,"message"=>__("Entered OTP is not correct.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }


    /**
     * @param $data
     * @param int $websiteId
     * @return array
     */
    public function resetForgotPassword($data, $websiteId = 1)
    {
        try {
            if (!$this->checkCustomerWithSameMobileNo($data['mobile'], $websiteId)) {
                return ["status"=>false, "message"=>__("Customer does not exists.")];
            }

            $collection = $this->_customerFactory->create()->getCollection()
                ->addFieldToFilter("mobilenumber", $data['mobile']);
            if (count($collection) == 1) {
                $item = $collection->getFirstItem();
                $customer = $this->_customerFactory->create();
                $customer = $customer->setWebsiteId($websiteId);
                $customer = $customer->loadByEmail($item->getEmail());
                $customer->setRpToken($item->getRpToken());
                $customer->setPassword($data['password']);

                $customerData = $customer->getDataModel();
                $customerData->setCustomAttribute('mobilenumber', $data['mobile']);
                $customer->updateData($customerData);
                $customer->save();
                return ["status"=>true, "message"=>__("Your password changed successfully.")];
            }

            return ["status"=>false, "message"=>__("Customer does not exists.")];
        } catch (\Exception $e) {
            return ["status"=>false, "message"=>$e->getMessage()];
        }
    }


    public function sendTestAPI($websiteId=1)
    {
        $testMobile = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_MOBILE, ScopeInterface::SCOPE_STORE);
        $testMessage = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_MSG, ScopeInterface::SCOPE_STORE);
        $testDlt = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_DLTID, ScopeInterface::SCOPE_STORE);
        return $this->callApiUrl($testMessage, $testMobile, $testDlt);
    }

    public function sendAdminTestAPI($websiteId=1)
    {
        $testMobile = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_MOBILE, ScopeInterface::SCOPE_WEBSITES,$websiteId);
        $testMessage = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_MSG, ScopeInterface::SCOPE_WEBSITES,$websiteId);
        $testDlt = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_DLTID, ScopeInterface::SCOPE_WEBSITES,$websiteId);
        if($this->getWhichApiUse()=='campaign'){
            $testDlt = $this->scopeConfig->getValue(self::MOBILELOGIN_TEST_COMPAIGNID,ScopeInterface::SCOPE_WEBSITES,$websiteId);
        }
        
        return $this->callApiUrl($testMessage, $testMobile, $testDlt);
    }

}