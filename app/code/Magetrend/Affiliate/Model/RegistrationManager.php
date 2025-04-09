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

namespace Magetrend\Affiliate\Model;

use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use Magetrend\Affiliate\Model\ResourceModel\Record\Balance\Collection;

class RegistrationManager
{
    public $date;

    public $customerFactory;

    public $accountManagement;

    public $accountFactory;

    public $account;

    public $transportBuilder;

    public $moduleHelper;

    public $mailHelper;

    public $storeManager;

    public $registry;

    public $customerRepository;

    public $customerRegistry;

    public $dataProcessor;

    public $customerViewHelper;

    public $registrationFields;

    public $remoteAddress;

    public $formFactory;

    public function __construct(
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\Config\RegistrationFields $registrationFields,
        \Magetrend\Affiliate\Helper\Mail $mailHelper,
        \Magetrend\Affiliate\Model\Account $account,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory,
        \Magetrend\Affiliate\Model\FormBuilder\FormFactory $formFactory,
        \Magetrend\Affiliate\Model\Mail\Template\TransportBuilder $transportBuilder,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Customer\Api\Data\CustomerInterfaceFactory $customerFactory,
        \Magento\Customer\Api\CustomerRepositoryInterface $customerRepository,
        \Magento\Customer\Api\AccountManagementInterface $accountManagement,
        \Magento\Store\Model\StoreManagerInterface $storeManager,
        \Magento\Framework\Registry $registry,
        \Magento\Customer\Model\CustomerRegistry $customerRegistry,
        \Magento\Framework\Reflection\DataObjectProcessor $dataProcessor,
        \Magento\Customer\Helper\View $customerViewHelper,
        \Magento\Framework\HTTP\PhpEnvironment\RemoteAddress $remoteAddress
    ) {
        $this->moduleHelper = $moduleHelper;
        $this->registrationFields = $registrationFields;
        $this->account = $account;
        $this->accountFactory = $accountFactory;
        $this->date = $date;
        $this->customerFactory = $customerFactory;
        $this->accountManagement = $accountManagement;
        $this->transportBuilder = $transportBuilder;
        $this->storeManager = $storeManager;
        $this->mailHelper = $mailHelper;
        $this->registry = $registry;
        $this->customerRepository = $customerRepository;
        $this->customerRegistry = $customerRegistry;
        $this->dataProcessor = $dataProcessor;
        $this->customerViewHelper = $customerViewHelper;
        $this->remoteAddress = $remoteAddress;
        $this->formFactory = $formFactory;
    }

    public function registerAccount($data)
    {
        $form = $this->formFactory->create()
            ->load($this->moduleHelper->getRegistrationFormId());

        $registrationData = $form->validateData($data);

        $registrationData['ip'] = isset($registrationData['ip'])?$postData['ip']:
            $this->remoteAddress->getRemoteAddress();

        $registrationData['website_id'] = $websiteId = $this->storeManager->getStore($data['store_id'])->getWebsiteId();
        $registrationData['store_id'] = $data['store_id'];
        $registrationData['created_at'] = $this->date->gmtDate('Y-m-d H:i:s');
        $registrationData['status'] = \Magetrend\Affiliate\Model\Account::STATUS_NEW;

        $newAccount = $this->accountFactory->create()
            ->setData($registrationData)
            ->save();

        $form->saveData($data, \Magetrend\Affiliate\Model\Account::DATA_OBJECT, $newAccount->getId());

        if ($this->moduleHelper->isRequireToConfirmSignUp($registrationData['store_id'])) {
            $this->mailHelper->sendEmail(
                \Magetrend\Affiliate\Helper\Data::XML_PATH_REGISTRATION_CONFIRMATION_EMAIL_TEMPLATE,
                $newAccount->getEmail(),
                $newAccount->getData(),
                $newAccount->getStoreId()
            );
        } else {
            $this->confirmAccount($newAccount->getId());
        }
    }

    public function confirmAccount($affiliateAccountId)
    {
        $customerAccountWasCreated = false;
        $affiliateAccount = $this->accountFactory->create()
            ->load($affiliateAccountId);
        $websiteId = $affiliateAccount->getWebsiteId();
        $storeId = $affiliateAccount->getStoreId();

        $registrationData = $affiliateAccount->getRegistrationFormData(true);
        $name = $this->moduleHelper->extractAccountName($registrationData);

        try {
            $customerAccount = $this->customerRepository->get($affiliateAccount->getEmail(), $websiteId);
        } catch (NoSuchEntityException $e) {
            $customer = $this->customerFactory->create();
            $customer->setWebsiteId($websiteId);
            $customer->setFirstname($name['firstname']);
            $customer->setLastname($name['lastname']);
            $customer->setEmail($affiliateAccount->getEmail());
            $this->registry->register('affiliate_disable_new_account_email', 1, true);
            $customerAccount = $this->accountManagement->createAccount($customer);
            $customerAccountWasCreated = true;
        }

        $defaultProgram = $this->moduleHelper->getDefaultAffiliateProgram($websiteId);
        $accountId = $customerAccount->getId();
        $affiliateAccount->addData([
            'customer_id' => $accountId,
            'base_balance' => 0,
            'balance' => 0,
            'base_available_balance' => 0,
            'available_balance' => 0,
            'reserved_balance' => 0,
            'base_reserved_balance' => 0,
            'referral_code' => $this->account->generateReferralCode(),
            'base_currency' => $this->moduleHelper->getBaseCurrencyCode($websiteId),
            'currency' => $this->moduleHelper->getWebsiteCurrencyCode($websiteId),
            'paypal_account_link' => '',
            'require_recalculate' => 0,
            'program' => $defaultProgram,
            'status' => \Magetrend\Affiliate\Model\Account::STATUS_ACTIVE
        ])->save();

        $customerData = $this->getFullCustomerObject($customerAccount);

        if ($customerAccountWasCreated) {
            $this->mailHelper->sendEmail(
                \Magetrend\Affiliate\Helper\Data::XML_PATH_REGISTRATION_CREATE_PASSWORD_TEMPLATE,
                $customerAccount->getEmail(),
                ['customer' => $customerData, 'affiliate' => $affiliateAccount],
                $storeId
            );
        } else {
            $this->mailHelper->sendEmail(
                \Magetrend\Affiliate\Helper\Data::XML_PATH_REGISTRATION_WELCOME_EMAIL_TEMPLATE,
                $customerAccount->getEmail(),
                ['customer' => $customerData, 'affiliate' => $affiliateAccount],
                $storeId
            );
        }
        return true;
    }

    private function getFullCustomerObject($customer)
    {
        $mergedCustomerData = $this->customerRegistry->retrieveSecureData($customer->getId());
        $customerData = $this->dataProcessor
            ->buildOutputDataArray($customer, \Magento\Customer\Api\Data\CustomerInterface::class);
        $mergedCustomerData->addData($customerData);
        $mergedCustomerData->setData('name', $this->customerViewHelper->getCustomerName($customer));
        return $mergedCustomerData;
    }

    public function delete($id)
    {
        $this->registrationFactory->create()
            ->load($id)
            ->setIsDeleted(1)
            ->save();

        return true;
    }
}
