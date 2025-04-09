<?php
/**
 * MB "Vienas bitas" (Magetrend.com)
 *
 * @category MageTrend
 * @package  Magetend/Affiliate
 * @author   Edvinas St. <edwin@magetrend.com>
 * @license  http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * @link     http://www.magetrend.com/magento-2-affiliate
 */

namespace Magetrend\Affiliate\Helper;

class Data extends \Magento\Framework\App\Helper\AbstractHelper
{
    const REFERRAL_PARAM_KEY = 'r';

    const REFERRAL_CODE_COOKIE_NAME = 'mtafr';

    const MIN_PAGE_LIMIT = 10;

    const XML_PATH_GENERAL_IS_ACTIVE = 'affiliate/general/is_active';

    const XML_PATH_WITHDRAWAL_MINIMUM_AMOUNT = 'affiliate/withdrawal/minimum_amount';

    const XML_PATH_WITHDRAWAL_TIME_ON_HOLD = 'affiliate/withdrawal/time_on_hold';

    const XML_PATH_REGISTRATION_CREATE_PASSWORD_TEMPLATE = 'affiliate/registration/create_password';

    const XML_PATH_REGISTRATION_WELCOME_EMAIL_TEMPLATE = 'affiliate/registration/welcome_email';

    const XML_PATH_REGISTRATION_CONFIRMATION_EMAIL_TEMPLATE = 'affiliate/registration/confirmation_email';

    const XML_PATH_EMAIL_SENDER_EMAIL = 'affiliate/email/sender_email';

    const XML_PATH_EMAIL_SENDER_NAME = 'affiliate/email/sender_name';

    const XML_PATH_REGISTRATION_SKIP_REVIEW = 'affiliate/registration/skip_review';

    const XML_GENERAL_REFERRAL_CODE_LENGTH = 'affiliate/general/referral_code_length';

    const XML_REGISTRATION_DEFAULT_AFFILIATE_PROGRAM = 'affiliate/registration/default_program';

    const XML_PATH_AFFILIATE_REGISTRATION_FORM = 'affiliate/registration/form';

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrencyInterface;

    public $customerSession;

    public $accountFactory;

    private $currentAccount = null;

    public $storeManager;

    public $accountHelper;

    public $currencyHelper;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magento\Directory\Helper\Data $currencyHelper
    ) {
        $this->customerSession = $customerSession;
        $this->accountFactory = $accountFactory;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        $this->storeManager = $storeManager;
        $this->accountHelper = $accountHelper;
        $this->currencyHelper = $currencyHelper;
        parent::__construct($context);
    }

    public function isActive($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_GENERAL_IS_ACTIVE,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getMinimumWithdrawAmount($format = false, $account = null)
    {
        if ($account == null) {
            $currency = $this->getBaseCurrencyCode();
            $websiteId = null;
        } else {
            $currency = $account->getCurrency();
            $websiteId = $account->getWebsiteId();
        }

        $amount = $this->scopeConfig->getValue(
            self::XML_PATH_WITHDRAWAL_MINIMUM_AMOUNT,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );

        if (!$format) {
            return $amount;
        }

        return $this->accountHelper->formatPrice($amount, $currency);
    }

    public function formatPrice($price, $currencyCode)
    {
        $formattedPrice = $this->priceCurrencyInterface->format(
            $price,
            false,
            \Magento\Framework\Pricing\PriceCurrencyInterface::DEFAULT_PRECISION,
            0,
            $currencyCode
        );
        return $formattedPrice;
    }

    public function getTimeOnHold($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_WITHDRAWAL_TIME_ON_HOLD,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getSenderName($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER_NAME,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getSenderEmail($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_PATH_EMAIL_SENDER_EMAIL,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getReferralCodeLength($store = null)
    {
        return $this->scopeConfig->getValue(
            self::XML_GENERAL_REFERRAL_CODE_LENGTH,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );
    }

    public function getDefaultAffiliateProgram($websiteId = null)
    {
        $defaultProgramList = $this->scopeConfig->getValue(
            self::XML_REGISTRATION_DEFAULT_AFFILIATE_PROGRAM,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
        if (empty($defaultProgramList)) {
            return [];
        }

        return explode(',', $defaultProgramList);
    }

    public function getBaseCurrencyCode($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_BASE,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    public function getWebsiteCurrencyCode($websiteId = null)
    {
        return $this->scopeConfig->getValue(
            \Magento\Directory\Model\Currency::XML_PATH_CURRENCY_DEFAULT,
            \Magento\Store\Model\ScopeInterface::SCOPE_WEBSITE,
            $websiteId
        );
    }

    public function isRequireToConfirmSignUp($store = null)
    {
        $value = $this->scopeConfig->getValue(
            self::XML_PATH_REGISTRATION_SKIP_REVIEW,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        if ($value == 1) {
            return false;
        }
        return true;
    }

    public function currencyConvert($amount, $from, $to = null, $round = true, $roundNumber = 4)
    {
        $amount = $this->currencyHelper->currencyConvert($amount, $from, $to);

        if (!$round) {
            return $amount;
        }

        return number_format($amount + 0.00001, $roundNumber, '.', '');
    }

    public function getRegistrationFormId($store = 0)
    {
        $formId = $this->scopeConfig->getValue(
            self::XML_PATH_AFFILIATE_REGISTRATION_FORM,
            \Magento\Store\Model\ScopeInterface::SCOPE_STORE,
            $store
        );

        return $formId;
    }

    public function extractAccountName($registrationData)
    {
        $firstName = __('Unknown');
        $lastName = __('Unknown');

        if (isset($registrationData['fullname']) && !empty($registrationData['fullname'])) {
            $tmpName = explode(' ', $registrationData['fullname']);
            if (isset($tmpName[1])) {
                $lastName = end($tmpName);
                array_pop($tmpName);
            }
            $firstName = implode(' ', $tmpName);
        }

        if (isset($registrationData['firstname']) && !empty($registrationData['firstname'])) {
            $firstName = $registrationData['firstname'];
        }

        if (isset($registrationData['lastname']) && !empty($registrationData['lastname'])) {
            $lastName = $registrationData['lastname'];
        }

        return [
            'firstname' => $firstName,
            'lastname' => $lastName,
        ];
    }
}
