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

use Magento\Customer\Model\Customer;
use Magento\Framework\Exception\LocalizedException;
use Magetrend\Affiliate\Model\ResourceModel\Record\Balance\Collection;

class Account extends \Magento\Framework\Model\AbstractModel
{
    const DATA_OBJECT = 'account';

    const STATUS_NEW = 'new';

    const STATUS_CANCELED = 'canceled';

    const STATUS_CONFIRMED = 'confirmed';

    const STATUS_ACTIVE = 'active';

    const STATUS_INACTIVE = 'inactive';

    const STATUS_DELETED = 'deleted';

    public $accountHelper;

    public $commissionManager;

    public $balanceManager;

    public $balanceCollectionFactory;

    public $balanceFactory;

    public $date;

    public $encryptor;

    public $collectionFactory;

    public $customerFactory;

    public $json;

    public $programCollectionFactory;

    public $dataCollectionFactory;

    public $moduleHelper;

    public $emailAddressValidator;

    private $customer = null;

    public function __construct(
        \Magento\Framework\Model\Context $context,
        \Magento\Framework\Registry $registry,
        \Magetrend\Affiliate\Helper\Account $accountHelper,
        \Magetrend\Affiliate\Helper\Data $moduleHelper,
        \Magetrend\Affiliate\Model\CommissionManager $commissionManager,
        \Magetrend\Affiliate\Model\BalanceManager $balanceManager,
        \Magetrend\Affiliate\Model\ResourceModel\Record\Balance\CollectionFactory $balanceCollectionFactory,
        \Magetrend\Affiliate\Model\Record\BalanceFactory $balanceFactory,
        \Magento\Framework\Stdlib\DateTime\DateTime $date,
        \Magento\Framework\Encryption\EncryptorInterface $encryptor,
        \Magetrend\Affiliate\Model\ResourceModel\Account\CollectionFactory $collectionFactory,
        \Magento\Customer\Model\CustomerFactory $customerFactory,
        \Magento\Framework\Json\Helper\Data $json,
        \Magetrend\Affiliate\Model\ResourceModel\Program\CollectionFactory $programCollectionFactory,
        \Magetrend\Affiliate\Model\ResourceModel\FormBuilder\Data\CollectionFactory $dataCollectionFactory,
        \Laminas\Validator\EmailAddress $emailAddressValidator,
        \Magento\Framework\Model\ResourceModel\AbstractResource $resource = null,
        \Magento\Framework\Data\Collection\AbstractDb $resourceCollection = null,
        array $data = []
    ) {
        $this->accountHelper = $accountHelper;
        $this->commissionManager = $commissionManager;
        $this->balanceManager = $balanceManager;
        $this->balanceCollectionFactory = $balanceCollectionFactory;
        $this->balanceFactory = $balanceFactory;
        $this->date = $date;
        $this->encryptor = $encryptor;
        $this->collectionFactory = $collectionFactory;
        $this->customerFactory = $customerFactory;
        $this->json = $json;
        $this->programCollectionFactory = $programCollectionFactory;
        $this->moduleHelper = $moduleHelper;
        $this->dataCollectionFactory = $dataCollectionFactory;
        $this->emailAddressValidator = $emailAddressValidator;
        parent::__construct($context, $registry, $resource, $resourceCollection, $data);
    }

    /**
     * Internal constructor, that is called from real constructor
     * @return void
     */
    //@codingStandardsIgnoreLine
    protected function _construct()
    {
        $this->_init('Magetrend\Affiliate\Model\ResourceModel\Account');
    }

    public function getFormattedPayoutAmount()
    {
        return $this->accountHelper->formatPrice($this->getPayoutAmount(), $this->getCurrency());
    }

    public function getFormattedBalance()
    {
        return $this->accountHelper->formatPrice($this->getBalance(), $this->getCurrency());
    }

    public function getFormattedAvailableBalance()
    {
        return $this->accountHelper->formatPrice($this->getAvailableBalance(), $this->getCurrency());
    }

    public function getFormattedReservedBalance()
    {
        return $this->accountHelper->formatPrice($this->getReservedBalance(), $this->getCurrency());
    }

    public function updateAccount($data)
    {
        $this->validateAccountData($data);

        $this->setPaypalAccountEmail($data['paypal_account_email'])
            ->save();
    }

    public function validateAccountData($data)
    {
        if (!isset($data['paypal_account_email']) || empty($data['paypal_account_email'])) {
            throw new LocalizedException(__('Paypal Account Email address is required'));
        }

        if (!$this->emailAddressValidator->isValid($data['paypal_account_email'])) {
            throw new LocalizedException(__('Paypal account email address is not valid.'));
        }

        return true;
    }

    public function getBalance()
    {
        if ($this->getRequireRecalculate() == 1) {
            $this->recalculateBalance();
        }

        return parent::getBalance();
    }

    public function getReservedBalance()
    {
        if ($this->getRequireRecalculate() == 1) {
            $this->recalculateBalance();
        }

        return parent::getReservedBalance();
    }

    public function getAvailableBalance()
    {
        if ($this->getRequireRecalculate() == 1) {
            $this->recalculateBalance();
        }

        return parent::getAvailableBalance();
    }

    public function recalculateBalance()
    {
        if (!$this->getId()) {
            return;
        }
        $this->balanceManager->updateAccountBalance($this);
        $this->setRequireRecalculate(0)
            ->save();
    }

    public function generateReferralCode()
    {
        $refCodeList = [];
        $codeLenght = $this->moduleHelper->getReferralCodeLength();
        for ($i = 0; $i < 20; $i++) {
            $refCodeList[] = substr($this->encryptor->getHash(time().' '.rand(0, 9999). ' '.$i), 0, $codeLenght);
        }

        $collection = $this->collectionFactory->create()
            ->addFieldToFilter('referral_code', ['in' => $refCodeList]);
        $ignore = [];
        if ($collection->getSize() > 0) {
            foreach ($collection as $item) {
                $ignore[$item->getReferralCode()] = 1;
            }
        }

        foreach ($refCodeList as $code) {
            if (!isset($ignore[$code])) {
                return $code;
            }
        }

        throw new LocalizedException(__('Unable to create new referral code'));
    }

    public function getCustomer()
    {
        if ($this->customer == null) {
            $this->customer = $this->customerFactory->create();
            $this->customer->load($this->getCustomerId());
        }
        return $this->customer;
    }

    public function beforeSave()
    {
        $program = $this->getProgram();
        if (is_array($program)) {
            $this->setProgram($this->json->jsonEncode($program));
        }

        return parent::beforeSave();
    }

    protected function _afterLoad()
    {
        $program = $this->getProgram();
        if (!is_array($program) && !empty($program)) {
            $this->setProgram($this->json->jsonDecode($program));
        }

        return parent::_afterLoad();
    }

    public function getProgramCollection()
    {
        $colection = $this->programCollectionFactory->create()
            ->addFieldToFilter('entity_id', ['in' => $this->getProgram()]);
        return $colection;
    }

    public function getRegistrationFormData($keyValueMap = false)
    {
        $dataCollection = $this->dataCollectionFactory->create()
            ->addObjectFilter($this->getId(), self::DATA_OBJECT);
        if (!$keyValueMap) {
            return $dataCollection;
        }
        $keyValue = [];
        foreach ($dataCollection as $item) {
            $keyValue[$item->getFieldKey()] = $item->getFieldValue();
        }

        return $keyValue;
    }
}
