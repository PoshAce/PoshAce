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

class Account extends \Magento\Framework\App\Helper\AbstractHelper
{
    const REFERRAL_PARAM_KEY = 'r';

    /**
     * @var \Magento\Framework\Pricing\PriceCurrencyInterface
     */
    public $priceCurrencyInterface;

    public $customerSession;

    public $accountFactory;

    private $currentAccount = null;

    public function __construct(
        \Magento\Framework\App\Helper\Context $context,
        \Magento\Customer\Model\Session $customerSession,
        \Magento\Framework\Pricing\PriceCurrencyInterface $priceCurrencyInterface,
        \Magetrend\Affiliate\Model\AccountFactory $accountFactory
    ) {
        $this->customerSession = $customerSession;
        $this->accountFactory = $accountFactory;
        $this->priceCurrencyInterface = $priceCurrencyInterface;
        parent::__construct($context);
    }

    public function getReferralCode()
    {
        return $this->getCurrentAccount()->getReferralCode();
    }

    /**
     * @return \Magetrend\Affiliate\Model\Account
     */
    public function getCurrentAccount()
    {
        if ($this->currentAccount == null) {
            $this->currentAccount = $this->accountFactory->create()
                ->load($this->customerSession->getCustomerId(), 'customer_id');
        }

        return $this->currentAccount;
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
}
